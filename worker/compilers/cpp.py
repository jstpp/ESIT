import math
import os
import subprocess
import sys
import time
import logging

logger = logging.getLogger("esit.worker")
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), "..")))
from api.config import config


def run(submission):
    try:
        root_dir = os.path.abspath(
            str(os.path.dirname(os.path.realpath(__file__)))
            + "/../solutions/"
            + str(submission["submission_id"])
        )
        code_file = root_dir + "/code/" + submission["submission_id"] + ".cpp"
        output_dir = root_dir + "/output"
        time_dir = root_dir + "/time"
        misc_dir = root_dir + "/misc"
        input_dir = os.path.abspath(
            str(os.path.dirname(os.path.realpath(__file__)))
            + "/../inout/"
            + str(submission["problem_id"])
            + "/in"
        )

    except Exception as e:
        logger.exception("A compiler exception occured - not enough arguments when calling script: " +
            str(e),
            " -> sys.argv length: ",
            str(len(sys.argv))
        )
        return "fail"

    genfile = sys.stdout

    try:
        logger.info(
            "Compilation of submission " +
            str(submission["submission_id"])
        )
        debugfilecompilation = open(
            misc_dir + "/debug-compilation.log", "w"
        )
        quest = subprocess.run(
            [
                "nsjail",
                "--config",
                os.path.abspath(
                    str(os.path.dirname(os.path.realpath(__file__)))
                    + "/../sandboxing/policy/cppexecpolicy-compilation.cfg"
                ),
                "-T",
                "/tmp",
                "-B",
                root_dir,
                "-B",
                misc_dir,
                "-R",
                code_file,
                "--cwd",
                root_dir,
                "--time_limit",
                str(math.ceil(config.worker_max_compilation_time)),
                "--rlimit_as",
                str(config.worker_max_compilation_memory),
                "--",
                "/usr/bin/g++",
                "-std=c++20",
                "-pedantic",
                "-Wall",
                "-ftime-report",
                "-o",
                (misc_dir + "/compiled.out"),
                code_file,
            ],
            capture_output=True,
            text=True,
        )
        sys.stdout = debugfilecompilation
        print(quest.stderr)
    except Exception as e:
        logger.exception(f"A compiler exception occured - general compilation error: {e}")
        return "fail"

    for turn in submission["tests"]:
        logger.info(
            "Execution of submission " +
            str(submission["submission_id"]) +
            " (test" +
            str(turn["TEST_ID"]) + ")"
        )
        try:
            genfile = sys.stdout
            logfile = open(output_dir + "/" + str(turn["TEST_ID"]) + ".log", "w")
            debugfilerun = open(
                misc_dir + "/debug-run-" + str(turn["TEST_ID"]) + ".log", "w"
            )
        except Exception:
            logger.exception("A compiler exception occured - compilation error for test "
                + str(turn["TEST_ID"])
            )
            return "fail"

        try:
            sys.stdout = debugfilerun
            quest2 = subprocess.run(
                [
                    "/usr/bin/time",
                    "-f",
                    "\t%U",
                    "-o",
                    os.path.abspath(time_dir + "/" + str(turn["TEST_ID"]) + ".log"),
                    "nsjail",
                    "--config",
                    os.path.abspath(
                        str(os.path.dirname(os.path.realpath(__file__)))
                        + "/../sandboxing/policy/cppexecpolicy-run.cfg"
                    ),
                    "-B",
                    root_dir,
                    "-B",
                    misc_dir,
                    "-R",
                    code_file,
                    "--cwd",
                    root_dir,
                    "--time_limit",
                    str(math.ceil(turn["max_time"])),
                    "--rlimit_as",
                    str(turn["max_memory"]),
                    "--",
                    (misc_dir + "/compiled.out"),
                    code_file,
                ],
                stdin=open(input_dir + "/" + str(turn["TEST_ID"]) + ".in"),
                capture_output=True,
                text=True,
            )
            print(quest2.stderr)
            sys.stdout = logfile
            print(quest2.stdout)

            if quest.returncode==0:
                turn["result"] = quest2.stdout
                turn["returncode"] = quest2.returncode
            else:
                turn["result"] = quest.stdout
                turn["returncode"] = quest.returncode

            sys.stdout = genfile
            logfile.close()
        except Exception:
            sys.stdout = genfile
            logfile.close()

    return "success"
