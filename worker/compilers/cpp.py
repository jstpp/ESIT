import sys, os
import subprocess
import datetime, json, math

sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
import api.config.config as config

def run(submission):
    try:
        root_dir = str(os.path.dirname(os.path.realpath(__file__))) + "/../solutions/" + submission['submission_id']
        code_file = root_dir + "/code/" + submission['submission_id'] + ".cpp"
        output_dir = root_dir + "/output"
        time_dir = root_dir + "/time"
        misc_dir = root_dir + "/misc"
        input_dir = str(os.path.dirname(os.path.realpath(__file__))) + "/../inout/" + submission['problem_id'] + "/in"

    except Exception as e:
        print("A compiler exception occured - not enough arguments when calling script: ", e, " -> sys.argv length: ", len(sys.argv))
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
            quest = subprocess.run(["nsjail", "--config", (str(os.path.dirname(os.path.realpath(__file__))) + "/../sandboxing/policy/cppexecpolicy.cfg"), "-T", root_dir, "-B", misc_dir, "-R", code_file, "--cwd", root_dir, "--time_limit", str(math.ceil(config.worker_max_compilation_time)),"--rlimit_as", str(config.worker_max_compilation_memory),"--quiet","--","/usr/bin/g++","-std=c++20","-pedantic","-Wall","-ftime-report","-o",(misc_dir + "/" + str(turn['TEST_ID']) + ".out"),code_file],capture_output=True,text=True)

            sys.stdout = logfile
            quest2 = subprocess.run(["/usr/bin/time","-f","\t%U","-o",(time_dir + "/" + str(turn['TEST_ID']) + ".log"),"nsjail", "--config", (str(os.path.dirname(os.path.realpath(__file__))) + "/../sandboxing/policy/cppexecpolicy.cfg"), "-T", root_dir, "-B", misc_dir, "-R", code_file, "--cwd", root_dir, "--time_limit", str(math.ceil(turn['max_time'])),"--rlimit_as", str(turn['max_memory']),"--quiet","--",(misc_dir + "/" + str(turn['TEST_ID']) + ".out"),code_file],stdin=open(input_dir + "/" + str(turn['TEST_ID']) + ".in"),capture_output=True,text=True)
            print(quest2.stdout)
            turn['result'] = quest2.stdout 
                
            sys.stdout = genfile
            logfile.close()
        except Exception as e:
            sys.stdout = genfile
            logfile.close()

    return "success"
