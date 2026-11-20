import logging
import math
import os
import subprocess
import sys

logger = logging.getLogger("esit.worker")
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), "..")))
from api.config import config


def run(submission):
    try:
        root_dir = os.path.abspath(
            str(os.path.dirname(os.path.realpath(__file__)))
            + "/../solutions/"
            + str(submission.submission_id)
        )
        code_file = root_dir + "/code/Main.java"
        output_dir = root_dir + "/output"
        time_dir = root_dir + "/time"
        misc_dir = root_dir + "/misc"
        input_dir = os.path.abspath(
            str(os.path.dirname(os.path.realpath(__file__)))
            + "/../inout/"
            + str(submission.problem_id)
            + "/in"
        )

        os.rename(
            root_dir + "/code/" + str(submission.submission_id) + ".java", code_file
        )
    except Exception as e:
        logger.exception(
            "A compiler exception occured - not enough arguments when calling script: "
            + str(e)
            + " -> sys.argv length: "
            + str(len(sys.argv))
        )
        return "fail"

    try:
        logger.info("Compilation of submission " + str(submission.submission_id))
        compilation_debug_file = misc_dir + "/debug-compilation.log"

        with open(compilation_debug_file, "wb") as compdebugfile:
            quest = subprocess.run(
                [
                    "nsjail",
                    "--config",
                    os.path.abspath(
                        str(os.path.dirname(os.path.realpath(__file__)))
                        + "/../sandboxing/policy/javaexecpolicy-compilation.cfg"
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
                    str(config.worker_max_compilation_memory * 8),
                    "--",
                    "/usr/bin/javac",
                    "-J-Xmx" + str(config.worker_max_compilation_memory) + "M",
                    "-d",
                    "./misc",
                    "./code/Main.java",
                ],
                stdout=compdebugfile,
                stderr=compdebugfile
            )

    except Exception as e:
        logger.exception(
            f"A compiler exception occured - general compilation error: {e}"
        )
        return "fail"

    for turn in submission.tests:
        logger.info(
            "Execution of submission "
            + str(submission.submission_id)
            + " (test"
            + str(turn.TEST_ID)
            + ")"
        )

        debug_file = misc_dir + "/debug-run-" + str(turn.TEST_ID) + ".log"
        output_file = output_dir + "/" + str(turn.TEST_ID) + ".log"
        input_file = input_dir + "/" + str(turn.TEST_ID) + ".in"

        try:
            with open(output_file, "wb") as outputfile, open(debug_file, "wb") as debugfile, open(input_file, "rb") as inputfile:
                quest2 = subprocess.run(
                    [
                        "/usr/bin/time",
                        "-f",
                        "\t%U",
                        "-o",
                        os.path.abspath(time_dir + "/" + str(turn.TEST_ID) + ".log"),
                        "nsjail",
                        "--config",
                        os.path.abspath(
                            str(os.path.dirname(os.path.realpath(__file__)))
                            + "/../sandboxing/policy/javaexecpolicy-run.cfg"
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
                        str(math.ceil(turn.max_time)),
                        "--rlimit_as",
                        str(turn.max_memory * 8),
                        "--",
                        "/usr/bin/java",
                        "-Xmx" + str(turn.max_memory) + "M",
                        "-cp",
                        misc_dir,
                        "Main",
                    ],
                    stdin=inputfile,
                    stdout=outputfile,
                    stderr=debugfile
                )


            with open(output_file, "r", encoding="utf-8", errors="replace") as outputfile:
                turn.result = outputfile.read()
            if quest.returncode == 0:
                turn.returncode = quest2.returncode
            else:
                turn.returncode = quest.returncode

        except Exception:
            turn.result = ""
            turn.returncode = 255
            logger.exception(f"An exception occurred.")

    return "success"
