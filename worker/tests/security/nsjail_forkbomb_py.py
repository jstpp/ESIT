import base64
import os
import shutil

import compilers.python


def prepare_submission(code):
    if not os.path.exists(
        os.path.abspath(str(os.path.dirname(os.path.realpath(__file__))) + "/../tmp/")
    ):
        os.mkdir(
            os.path.abspath(
                str(os.path.dirname(os.path.realpath(__file__))) + "/../tmp/"
            )
        )

    if not os.path.exists(
        os.path.abspath(
            str(os.path.dirname(os.path.realpath(__file__))) + "/../tmp/0-inout/"
        )
    ):
        os.mkdir(
            os.path.abspath(
                str(os.path.dirname(os.path.realpath(__file__))) + "/../tmp/0-inout"
            )
        )
        os.mkdir(
            os.path.abspath(
                str(os.path.dirname(os.path.realpath(__file__))) + "/../tmp/0-inout/in"
            )
        )
        os.mkdir(
            os.path.abspath(
                str(os.path.dirname(os.path.realpath(__file__))) + "/../tmp/0-inout/out"
            )
        )
        with open(
            os.path.abspath(str(os.path.dirname(os.path.realpath(__file__))))
            + "/../tmp/0-inout/in/0.in",
            "w",
        ) as f:
            f.write("11")
        with open(
            os.path.abspath(str(os.path.dirname(os.path.realpath(__file__))))
            + "/../tmp/0-inout/out/0.out",
            "w",
        ) as f:
            f.write("121")

    if os.path.exists(
        os.path.abspath(str(os.path.dirname(os.path.realpath(__file__))) + "/../tmp/0/")
    ):
        shutil.rmtree(
            os.path.abspath(
                str(os.path.dirname(os.path.realpath(__file__))) + "/../tmp/0"
            )
        )
    os.mkdir(
        os.path.abspath(str(os.path.dirname(os.path.realpath(__file__))) + "/../tmp/0")
    )
    os.mkdir(
        os.path.abspath(
            str(os.path.dirname(os.path.realpath(__file__))) + "/../tmp/0/code"
        )
    )
    os.mkdir(
        os.path.abspath(
            str(os.path.dirname(os.path.realpath(__file__))) + "/../tmp/0/misc"
        )
    )
    os.mkdir(
        os.path.abspath(
            str(os.path.dirname(os.path.realpath(__file__))) + "/../tmp/0/time"
        )
    )
    os.mkdir(
        os.path.abspath(
            str(os.path.dirname(os.path.realpath(__file__))) + "/../tmp/0/output"
        )
    )
    root_dir = os.path.abspath(
        str(os.path.dirname(os.path.realpath(__file__))) + "/../tmp/0/"
    )

    with open(root_dir + "/code/0.py", "w") as f:
        f.write(code)

    submission = {
        "submission_id": "0",
        "submission_type": "testing",
        "user_id": 0,
        "problem_id": 0,
        "submission_lang": "python",
        "tests": [
            {
                "TEST_ID": 0,
                "test_author_id": 0,
                "problem_id": 0,
                "max_time": 2,
                "max_memory": 256,
                "weight": 10,
            }
        ],
        "listenerUrl": "http:\\/\\/web",
        "submission_time": "2026-01-01 00:00:00",
        "submission_file": base64.b64encode(code.encode("utf-8")).decode("utf-8"),
    }

    return submission


code = """
import os

children = []

try:
    for i in range(200):
        pid = os.fork()

        if pid == 0:
            os._exit(0)

        children.append(pid)

except OSError as e:
    print("[OK] process creation limited:", type(e).__name__)

finally:
    for pid in children:
        try:
            os.waitpid(pid, 0)
        except ChildProcessError:
            pass
    
"""

submission = prepare_submission(code)
status = compilers.python.run(submission)

assert submission["tests"][0]["returncode"] == 0, submission["tests"][0]["returncode"]
