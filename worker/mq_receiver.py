#!/usr/bin/env python

import json
import logging
import os
import sys
import threading
import time
from logging.handlers import RotatingFileHandler

import pika

import api.lib
import compilers.cpp
import compilers.java
import compilers.python
import compilers.rust
import tests.run as tests
from api.config import config
from obj.submission import Submission


SUPPORTED_LANGUAGES = {"py", "cpp", "java", "rs"}
ENFORCED_TESTING = True
ENFORCED_LANDLOCK = True
QUIET_TESTING = False
CANARY_HEARTBEAT = 8 # in seconds


def setup_logging(log_directory, worker_id):
    os.makedirs(log_directory, exist_ok=True)

    logger = logging.getLogger(f"esit.worker.{worker_id}")
    logger.setLevel(logging.INFO)
    logger.propagate = False

    if logger.handlers:
        return logger

    formatter = logging.Formatter(
        fmt="%(asctime)s [%(levelname)s] %(message)s",
        datefmt="%Y-%m-%d %H:%M:%S",
    )

    file_handler = RotatingFileHandler(
        os.path.join(log_directory, f"worker_{worker_id}.log"),
        maxBytes=10 * 1024 * 1024,
        backupCount=5,
        encoding="utf-8",
    )

    console_handler = logging.StreamHandler(sys.stderr)

    file_handler.setFormatter(formatter)
    console_handler.setFormatter(formatter)

    logger.addHandler(file_handler)
    logger.addHandler(console_handler)

    return logger


def handle_thread_exception(args):
    logger = logging.getLogger(f"esit.{args.thread.name}")
    logger.error(
        "Unhandled exception in thread %s",
        args.thread.name,
        exc_info=(
            args.exc_type,
            args.exc_value,
            args.exc_traceback,
        ),
    )


def validate_submission(data):
    if not data:
        raise TypeError("No submission provided")
    if not isinstance(data, Submission):
        raise TypeError("Submission must be a JSON object")

    submission_id = data.submission_id
    problem_id = data.problem_id
    language = data.submission_lang

    if not isinstance(submission_id, int) or isinstance(submission_id, bool):
        raise ValueError("Invalid submission_id")

    if submission_id <= 0:
        raise ValueError("submission_id must be positive")

    if not isinstance(problem_id, int) or isinstance(problem_id, bool):
        raise ValueError("Invalid problem_id")

    if problem_id <= 0:
        raise ValueError("problem_id must be positive")

    if language not in SUPPORTED_LANGUAGES:
        raise ValueError("Unsupported submission language")

    return data


def initialize_landlock(logger):
    try:
        from landlockpy import AccessFS, AccessNet, Ruleset

        with Ruleset(handled_fs=AccessFS.NONE, handled_net=AccessNet.CONNECT_TCP) as rs:
            rs.allow_port(53, AccessNet.CONNECT_TCP)
            rs.allow_port(80, AccessNet.CONNECT_TCP)
            rs.allow_port(443, AccessNet.CONNECT_TCP)
            rs.allow_port(int(config.rabbitmq_port), AccessNet.CONNECT_TCP)
            rs.restrict()
        logger.info("Landlock restrictions initialized successfully.")
        return True
    except Exception:
        if ENFORCED_LANDLOCK:
            logger.critical(
                "Landlock initialization failed. Worker initialization has been canceled."
            )
            sys.exit(1)
        else:
            logger.warning(
                "Landlock initialization failed. It may decrease level of worker security."
            )
        return False


def start_execution(logger, submission):
    if submission.submission_lang == "py":
        logger.info("Executing PYTHON script.")
        if not api.lib.send(compilers.python.run(submission), submission):
            raise Exception("API usage failed")
    elif submission.submission_lang == "cpp":
        logger.info("Executing C++ script.")
        if not api.lib.send(compilers.cpp.run(submission), submission):
            raise Exception("API usage failed")
    elif submission.submission_lang == "java":
        logger.info("Executing JAVA script.")
        if not api.lib.send(compilers.java.run(submission), submission):
            raise Exception("API usage failed")
    elif submission.submission_lang == "rs":
        logger.info("Executing RUST script.")
        if not api.lib.send(compilers.rust.run(submission), submission):
            raise Exception("API usage failed")
    else:
        raise Exception(f"Unsupported language: {submission.submission_lang}")


def connect_to_queue(logger, t_start, max_retries = 5):
    t = t_start
    while True:
        try:
            credentials = pika.PlainCredentials(
                config.rabbitmq_user, config.rabbitmq_password
            )
            connection = pika.BlockingConnection(
                pika.ConnectionParameters(
                    host=config.rabbitmq_host,
                    port=config.rabbitmq_port,
                    credentials=credentials,
                    heartbeat=180,
                    socket_timeout=10,
                )
            )
            channel = connection.channel()
            logger.info("Successfully connected to the queue.")
            break
        except Exception as e:
            t += 1

            if t-t_start > max_retries:
                logger.critical("Can't connect to RabbitMQ. Stopping worker...")
                sys.exit(1)

            logger.error(
                f"Can't connect to RabbitMQ: {e}. "
                f"Trying to reconnect in {2**t} seconds."
            )

            time.sleep(2**t)

    return connection, channel


def prepare_inout(logger, submission):
    try:
        logger.info(f"Preparing inout for submission {submission.submission_id}...")
        if os.path.isdir(
            os.path.dirname(os.path.realpath(__file__))
            + "/inout/"
            + str(submission.problem_id)
        ):
            logger.info(
                f'Inout set for submission {submission.submission_id} has been found in {os.path.dirname(os.path.realpath(__file__))
                + "/inout/"
                + str(submission.problem_id)}'
            )
            return True
        else:
            logger.info(
                f"Inout set for submission {submission.submission_id} not found. Asking main server to fill the gap..."
            )
            if api.lib.ask_for_inout(submission.to_dict()):
                return True
            else:
                logger.error(
                    f"Inout set for submission {submission.submission_id} not found. **Filling the gap failed!**"
                )
                return False
    except Exception as exception:
        raise Exception(f"mq_receiver.py: prepare_inout(): {exception}")


def canary(logger, device_name):
    while True:
        api.lib.ping(f"worker ({str(device_name)})", config.web_url, max(CANARY_HEARTBEAT,1))
        time.sleep(max(CANARY_HEARTBEAT-1,0))

def main(worker_id=1):
    try:
        logger = setup_logging(
            os.path.join(os.path.dirname(os.path.realpath(__file__)), "logs"), worker_id
        )

        logger.info("Worker initialization...")
        logger.info("Testing current configuration...")

        logger.info(f"Logging directory: worker_{worker_id}.log")

    except Exception as exception:
        raise Exception(f"mq_receiver.py: main(): {exception}")

    try:
        connection, channel = connect_to_queue(logger, t_start=1, max_retries=5)
        initialize_landlock(logger)

        threading.Thread(
            target=canary,
            kwargs={"device_name": worker_id, "logger": logger},
            name=f"canary.worker.{worker_id}",
            daemon=True
        ).start()

        def callback(ch, method, properties, body):
            try:
                submission = None
                try:
                    submission = validate_submission(
                        Submission.from_dict(json.loads(body))
                    )

                except (ValueError, TypeError):
                    logger.exception("Received invalid submission.")
                    ch.basic_nack(
                        delivery_tag=method.delivery_tag,
                        requeue=False,
                    )
                    return

                except Exception:
                    logger.exception("Undefined exception when processing submission.")
                    ch.basic_nack(
                        delivery_tag=method.delivery_tag,
                        requeue=False,
                    )
                    return

                logger.info(f"Received submission {submission.submission_id}")

                if not (prepare_inout(logger, submission)):
                    raise Exception(
                        f"An error occurred when worker was preparing inout for submission {submission.submission_id}."
                    )
                if not api.lib.prepare(submission.to_dict()):
                    raise Exception("Failed to prepare submission.")

                start_execution(logger, submission)

            except Exception:
                sid = "unknown"
                if (
                    submission
                    and isinstance(submission, Submission)
                    and submission.submission_id != "unknown"
                    and api.lib.send("fail", submission)
                ):
                    ch.basic_ack(delivery_tag=method.delivery_tag)
                    sid = submission.submission_id
                else:
                    ch.basic_nack(
                        delivery_tag=method.delivery_tag,
                        requeue=False,
                    )
                logger.exception(
                    f"Submission {sid} couldn't be evaluated. Sent to DLQ"
                )
            else:
                ch.basic_ack(
                    delivery_tag=method.delivery_tag,
                )

        channel.basic_qos(prefetch_count=1)
        channel.basic_consume(
            queue="esit", on_message_callback=callback, auto_ack=False
        )

        logger.info("Waiting for messages. To exit press CTRL+C")
        channel.start_consuming()
        logger.warning(f"Broker connection closed. ")

        try:
            while True:
                connection, channel = connect_to_queue(logger, t_start=1, max_retries=12)
                channel.basic_qos(prefetch_count=1)
                channel.basic_consume(
                    queue="esit", on_message_callback=callback, auto_ack=False
                )
                channel.start_consuming()
        except Exception as exception:
            logger.exception("An exception occured.")

    except Exception as exception:
        raise Exception(f"mq_receiver.py: main(): {exception}")


if __name__ == "__main__":
    workers = []

    try:
        if len(sys.argv) != 2:
            print(f"Usage: {sys.argv[0]} <number_of_workers>")
            sys.exit(1)

        if not tests.is_ok(QUIET_TESTING):
            if ENFORCED_TESTING:
                logging.getLogger("esit.worker").critical(
                    "Testing detected errors. Worker initialization has been canceled. To start the worker, fix all errors first."
                )
                sys.exit(1)
            else:
                logging.getLogger("esit.worker").warning(
                    "Testing detected errors. Worker initialization should be canceled but ENFORCED_TESTING is set to `False`. Ignored."
                )
        else:
            logging.getLogger("esit.worker").info("Testing finished. No errors found.")

        threading.excepthook = handle_thread_exception
        for i in range(int(sys.argv[1])):
            workers.append(
                threading.Thread(
                    target=main, kwargs={"worker_id": i + 1}, name=f"worker.{i + 1}"
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

    except Exception:
        logging.getLogger("esit.worker").exception(
            "An exception occurred during workers' startup."
        )
