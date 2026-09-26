#!/usr/bin/env python
import compilers.python, compilers.cpp
import tests.run as tests
import api.lib
import pika, sys, os, json, time
import pandas as pd
    
def connect_to_queue():
    try:
        credentials = pika.PlainCredentials('esit_user','123456') #Make some changes here
        connection = pika.BlockingConnection(pika.ConnectionParameters(host='rabbitmq', port='5672', credentials=credentials)) #and here!
        channel = connection.channel()
        print(str(time.ctime())+' | Successfully connected to the queue.')
    except Exception as e:
        print(str(time.ctime())+f' | Can\'t connect to the database: {e}. Trying to reconnect...')
        time.sleep(10)
        channel = connect_to_queue()

    return channel

def prepare_inout(submission):
    try:
        print(str(time.ctime())+f' | Preparing inout for submission {submission["submission_id"]}...')
        if(os.path.isdir(os.path.dirname(os.path.realpath(__file__)) + "/inout/" + str(submission['problem_id']))):
            print(str(time.ctime())+f' | Inout set for submission {submission["submission_id"]} has been found in ', os.path.dirname(os.path.realpath(__file__)) + "/inout/" + str(submission['problem_id']))
            return True
        else:
            print(str(time.ctime())+f' | Inout set for submission {submission["submission_id"]} not found. Asking main server to fill the gap...')
            if(api.lib.ask_for_inout(submission)):
                return True
            else:
                print(str(time.ctime())+f' | Inout set for submission {submission["submission_id"]} not found. **Filling the gap failed!**')
                return False
    except Exception as exception:
        raise Exception(f"EXCEPTION | mq_receiver.py: prepare_inout(): {exception}")

def main():
    try:
        global logfile
        orginal_stdout = sys.stdout
        logfile = open(os.path.dirname(os.path.realpath(__file__))+'/logs/worker.log', 'a', buffering=1)
        sys.stdout = logfile

        print(str(time.ctime())+' | Worker initialization...')
        print(str(time.ctime())+' | Testing current configuration...')
        if not tests.is_ok(quiet=True):
            print(str(time.ctime())+' | Testing detected errors. Worker initialization has been canceled. To start the worker, fix all errors first.')
            sys.exit(1)
        else:
            print(str(time.ctime())+' | Testing finished. No errors found.') 

        print(str(time.ctime())+' | Logging directory: '+str(os.path.abspath(os.getcwd()))+'/logs/worker.log')
    except Exception as exception:
        raise Exception(f"EXCEPTION | mq_receiver.py: main(): {exception}") 

    try:
        channel = connect_to_queue()
        try:
            from landlockpy import AccessNet, AccessFS, Ruleset
            with Ruleset(handled_fs=AccessFS.NONE,handled_net=AccessNet.CONNECT_TCP) as rs:
                rs.allow_port(53, AccessNet.CONNECT_TCP)
                rs.allow_port(80, AccessNet.CONNECT_TCP)
                rs.allow_port(443, AccessNet.CONNECT_TCP)
                rs.restrict()
            print(str(time.ctime())+" | Landlock restrictions initialized successfully.")   
            landlock_available = True
        except Exception as e:
            landlock_available = False
            print(str(time.ctime())+f" | Landlock initialization failed. It may decrease level of worker security. {e}")

        def callback(ch, method, properties, body):
            print(str(time.ctime())+f' | Received {body}')
            submission = json.loads(body)

            if not (prepare_inout(submission)):
                print(str(time.ctime())+f' | An error occured when worker was preparing inout for submission {submission["submission_id"]}.')
            
            api.lib.prepare(submission)
            logfile.flush()

            if (submission["submission_lang"]=="py"):
                print(str(time.ctime())+' | Executing PYTHON script.')
                api.lib.send(compilers.python.run(submission), submission)
            elif (submission["submission_lang"]=="cpp"):
                print(str(time.ctime())+' | Executing C++ script.')
                api.lib.send(compilers.cpp.run(submission), submission)

        channel.basic_consume(queue='esit', on_message_callback=callback, auto_ack=True)

        print(str(time.ctime())+' | Waiting for messages. To exit press CTRL+C')
        logfile.flush()
        channel.start_consuming()
    except Exception as exception:
        raise Exception(f"EXCEPTION | mq_receiver.py: main(): {exception}")
    

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print(str(time.ctime())+' | Interrupted.')
        logfile.flush()
        try:
            sys.exit(0)
        except SystemExit:
            os._exit(0)
