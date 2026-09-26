import sys, os
import subprocess
import pandas as pd
import datetime, math, time
    
def run(submission):
    try:
        root_dir = os.path.abspath(str(os.path.dirname(os.path.realpath(__file__))) + "/../solutions/" + str(submission['submission_id']))
        code_file = root_dir + "/code/" + submission['submission_id'] + ".py"
        output_dir = root_dir + "/output"
        time_dir = root_dir + "/time"
        misc_dir = root_dir + "/misc"
        input_dir = os.path.abspath(str(os.path.dirname(os.path.realpath(__file__))) + "/../inout/" + str(submission['problem_id']) + "/in")
        
    except Exception as e:
        print(str(time.ctime())+" | A compiler exception occured - " + str(e))
        return "fail"

    
    genfile = sys.stdout
    for turn in submission['tests']:
        print(str(time.ctime()), "| Testing submission", str(submission['submission_id']), "(test", str(turn['TEST_ID']) + ")")
        try:
            genfile = sys.stdout
            logfile = open(output_dir + "/" + str(turn['TEST_ID']) + ".log","w")
            debugfilerun = open(misc_dir + "/debug-run-" + str(turn['TEST_ID']) + ".log","w")
        except Exception as e:
            print(str(time.ctime())+" | A compiler exception occured - error for test " + str(turn['TEST_ID']))
            return "fail"

        try:
            quest = subprocess.run(["/usr/bin/time","-f","\t%U","-o",os.path.abspath(time_dir + "/" + str(turn['TEST_ID']) + ".log"),"nsjail", "--config", os.path.abspath(str(os.path.dirname(os.path.realpath(__file__))) + "/../sandboxing/policy/pythonexecpolicy-run.cfg"), "-T", root_dir, "-B", root_dir+"/misc", "-R", code_file, "--cwd", root_dir, "--time_limit", str(math.ceil(turn['max_time'])), "--rlimit_as", str(turn['max_memory']),"--", "/usr/local/bin/python3", code_file],stdin=open(input_dir + "/" + str(turn['TEST_ID']) + ".in"),capture_output=True,text=True)
            sys.stdout = debugfilerun
            print(quest.stderr)
            sys.stdout = logfile
            print(quest.stdout)
            turn['result'] = quest.stdout
            sys.stdout = genfile
            logfile.close()
                
        except Exception as e:
            print(e)
            sys.stdout = genfile
            logfile.close()
    
    return "success"