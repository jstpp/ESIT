#!/usr/bin/env python

import json
import os
import sys
import time
import threading

import logging
from logging.handlers import RotatingFileHandler

import pika

import api.lib
import api.config.config as config
import compilers.cpp
import compilers.java
import compilers.python
import tests.run as tests

def setup_logging(log_directory, worker_id):
    os.makedirs(log_directory, exist_ok=True)
    log_file = os.path.join(log_directory, f"worker_{worker_id}.log")

    formatter = logging.Formatter(
        fmt=f"%(asctime)s [%(levelname)s] %(message)s",
        datefmt="%Y-%m-%d %H:%M:%S",
    )

    file_handler = RotatingFileHandler(
        log_file,
        maxBytes=10 * 1024 * 1024,
        backupCount=5,
        encoding="utf-8",
    )

    console_handler = logging.StreamHandler(sys.stderr)

    file_handler.setFormatter(formatter)
    console_handler.setFormatter(formatter)

    logging.basicConfig(
        level=logging.INFO,
        handlers=[file_handler, console_handler],
        force=True,
    )

    logging.captureWarnings(True)
    return logging.getLogger("esit.worker")


def connect_to_queue(logger, t):
    try:
        credentials = pika.PlainCredentials(
            config.rabbitmq_user, config.rabbitmq_password
        )
        connection = pika.BlockingConnection(
            pika.ConnectionParameters(
                host="rabbitmq", port="5672", credentials=credentials  # Can be customized
            )
        )
        channel = connection.channel()
        logger.info(
            "Successfully connected to the queue."
        )
    except Exception as e:
        t *= 2
        logger.error(
            f"Can't connect to the database: {e}. Trying to reconnect in {t} seconds."
        )
        time.sleep(t)
        channel = connect_to_queue(logger, t)

    return channel


def prepare_inout(logger, submission):
    try:
        logger.info(f'Preparing inout for submission {submission["submission_id"]}...')
        if os.path.isdir(
            os.path.dirname(os.path.realpath(__file__))
            + "/inout/"
            + str(submission["problem_id"])
        ):
            logger.info(
                f'Inout set for submission {submission["submission_id"]} has been found in {os.path.dirname(os.path.realpath(__file__))
                + "/inout/"
                + str(submission["problem_id"])}'
            )
            return True
        else:
            logger.info(f"Inout set for submission {submission["submission_id"]} not found. Asking main server to fill the gap...")
            if api.lib.ask_for_inout(submission):
                return True
            else:
                logger.error(f"Inout set for submission {submission["submission_id"]} not found. **Filling the gap failed!**")
                return False
    except Exception as exception:
        raise Exception(f"mq_receiver.py: prepare_inout(): {exception}")


def main(
    EnforcedTesting=True,
    QuietTesting=True,  
    EnforcedLandlock=True,
    worker_id=1
):
    try:
        logger = setup_logging(
            os.path.join(os.path.dirname(os.path.realpath(__file__)), "logs"),
            worker_id
        )

        logger.info("Worker initialization...")
        logger.info("Testing current configuration...")

        if not tests.is_ok(QuietTesting):
            if EnforcedTesting:
                logger.critical(
                    "Testing detected errors. Worker initialization has been canceled. To start the worker, fix all errors first."
                )
                sys.exit(1)
            else:
                logger.warning(
                    "Testing detected errors. Worker initialization should be canceled but EnforcedTesting is set to `false`. Ignored."
                )
        else:
            logger.info("Testing finished. No errors found.")

        logger.info(f"Logging directory: worker_{worker_id}.log")

    except Exception as exception:
        raise Exception(f"mq_receiver.py: main(): {exception}")

    try:
        channel = connect_to_queue(logger, 2)
        try:
            from landlockpy import AccessFS, AccessNet, Ruleset

            with Ruleset(
                handled_fs=AccessFS.NONE, handled_net=AccessNet.CONNECT_TCP
            ) as rs:
                rs.allow_port(53, AccessNet.CONNECT_TCP)
                rs.allow_port(80, AccessNet.CONNECT_TCP)
                rs.allow_port(443, AccessNet.CONNECT_TCP)
                rs.restrict()
            logger.info("Landlock restrictions initialized successfully.")
        except Exception as e:
            if EnforcedLandlock:
                logger.critical(
                    "Landlock initialization failed. Worker initialization has been canceled."
                )
                sys.exit(1)
            else:
                logger.warning(
                    "Landlock initialization failed. It may decrease level of worker security."
                )

        def callback(ch, method, properties, body):
            try:
                submission = json.loads(body)
                logger.info(
                    f"Received submission {submission["submission_id"]}",
                )

                if not (prepare_inout(logger, submission)):
                    logger.error(
                        f"An error occured when worker was preparing inout for submission {submission["submission_id"]}."
                    )
                    raise Exception(
                        "An error occured when worker was preparing inout for submission"
                    )

                api.lib.prepare(submission)

                if submission["submission_lang"] == "py":
                    logger.info("Executing PYTHON script.")
                    api.lib.send(compilers.python.run(submission), submission)
                elif submission["submission_lang"] == "cpp":
                    logger.info("Executing C++ script.")
                    api.lib.send(compilers.cpp.run(submission), submission)
                elif submission["submission_lang"] == "java":
                    logger.info("Executing JAVA script.")
                    api.lib.send(compilers.java.run(submission), submission)

            except Exception as e:
                channel.basic_nack(
                    delivery_tag=method.delivery_tag,
                    requeue=False,
                )
                api.lib.send("fail", submission)
                logger.warning(f"Submission {submission["submission_id"]} couldn\'t be evaluated. Sent to DLQ")
            else:
                channel.basic_ack(
                    delivery_tag=method.delivery_tag,
                )

        channel.basic_consume(
            queue="esit", on_message_callback=callback, auto_ack=False
        )

        logger.info("Waiting for messages. To exit press CTRL+C")
        channel.start_consuming()
    except Exception as exception:
        raise Exception(f"mq_receiver.py: main(): {exception}")


if __name__ == "__main__":
    workers = []
    try:
        if len(sys.argv) != 2:
            print(f"Usage: {sys.argv[0]} <number_of_workers>")
            sys.exit(1)

        for i in range(int(sys.argv[1])):
            workers.append(
                threading.Thread(
                    target=main,
                    kwargs={"worker_id": i + 1},
                    name=f"worker-{i + 1}"
                )
            )
            workers[i].start()

        for worker in workers:
            worker.join()

    except KeyboardInterrupt:
        logging.getLogger("esit.worker").info("Interrupted.")
        try:
            sys.exit(0)
        except SystemExit:
            os._exit(0)
