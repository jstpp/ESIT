import sys, os
import subprocess
import pandas as pd
import datetime, math
    
def run(submission):
    try:
        root_dir = str(os.path.dirname(os.path.realpath(__file__))) + "/../solutions/" + submission['submission_id']
        code_file = root_dir + "/code/" + submission['submission_id'] + ".py"
        output_dir = root_dir + "/output"
        time_dir = root_dir + "/time"
        misc_dir = root_dir + "/misc"
        input_dir = str(os.path.dirname(os.path.realpath(__file__))) + "/../inout/" + submission['problem_id'] + "/in"
        
    except Exception as e:
        print("A compiler exception occured - not enough arguments when calling script")
        return "fail"

    
    genfile = sys.stdout
    for turn in submission['tests']:
        print(datetime.datetime.now(), " | Compilation of submission ", submission['submission_id'], " (test", turn['TEST_ID'], ")")
        try:
            genfile = sys.stdout
            logfile = open(output_dir + "/" + str(turn['TEST_ID']) + ".log","w")
        except Exception as e:
            print("A compiler exception occured - compilation error for test " + str(turn['TEST_ID']))
            return "fail"

        try:
            sys.stdout = logfile
            bledy = ""
            quest = subprocess.run(["/usr/bin/time","-f","\t%U","-o",(time_dir + "/" + str(turn['TEST_ID']) + ".log"),"nsjail", "--config", (str(os.path.dirname(os.path.realpath(__file__))) + "/../sandboxing/policy/pythonexecpolicy.cfg"), "-T", root_dir, "-B", root_dir+"/misc", "-R", code_file, "--cwd", root_dir, "--time_limit", str(math.ceil(turn['max_time'])), "--rlimit_as", str(turn['max_memory']), "--quiet","--", "/usr/local/bin/python3", code_file],stdin=open(input_dir + "/" + str(turn['TEST_ID']) + ".in"),capture_output=True,text=True)
            print(quest.stdout)
            turn['result'] = quest.stdout
            sys.stdout = genfile
            logfile.close()
                
        except Exception as e:
            print(e)
            sys.stdout = genfile
            logfile.close()
    
    return "success"