#!/usr/bin/env python
import compilers.python, compilers.cpp
import api.lib
import pika, sys, os, json, time
import pandas as pd
    
def connect_to_queue():
    try:
        credentials = pika.PlainCredentials('esit_user','123456') #Make some changes here
        connection = pika.BlockingConnection(pika.ConnectionParameters(host='rabbitmq', port='5672', credentials=credentials)) #and here!
        channel = connection.channel()
        print(str(time.ctime())+' | Successfully connected to the queue.')
    except:
        print(str(time.ctime())+' | Can\'t connect to the database. Trying to reconnect...')
        time.sleep(10)
        channel = connect_to_queue()

    return channel

def prepare_inout(submission):
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

def main():
    print(str(time.ctime())+' | Worker is starting...')
    print(str(time.ctime())+' | Logging directory: '+str(os.path.dirname(os.path.realpath(__name__)))+'/logs/worker.log')

    global logfile
    orginal_stdout = sys.stdout
    logfile = open(os.path.dirname(os.path.realpath(__file__))+'/logs/worker.log', 'a')
    sys.stdout = logfile

    channel = connect_to_queue()
    def callback(ch, method, properties, body):
        print(str(time.ctime())+f' | Received {body}')
        submission = json.loads(body)

        if not (prepare_inout(submission)):
            print(str(time.ctime())+f' | An error occured when worker was preparing inout for submission {submission["submission_id"]}.')
        
        api.lib.prepare(submission)
        logfile.flush()

        if (submission["submission_lang"]=="py"):
            api.lib.send(compilers.python.run(submission), submission)
        elif (submission["submission_lang"]=="cpp"):
            api.lib.send(compilers.cpp.run(submission), submission)

    channel.basic_consume(queue='esit', on_message_callback=callback, auto_ack=True)

    print(str(time.ctime())+' | Waiting for messages. To exit press CTRL+C')
    logfile.flush()
    channel.start_consuming()

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
