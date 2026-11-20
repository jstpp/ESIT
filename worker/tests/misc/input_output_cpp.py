import base64
import os
import shutil

import compilers.cpp
from obj.submission import Submission


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

    with open(root_dir + "/code/0.cpp", "w") as f:
        f.write(code)
    with open(root_dir + "/misc/debug-compilation.log", "w") as f:
        f.write("")

    submission = {
        "submission_id": "0",
        "submission_type": "testing",
        "user_id": 0,
        "problem_id": 0,
        "submission_lang": "cpp",
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

    return Submission.from_dict(submission)


code = """
#include <iostream>
using namespace std;

int main() {
    int n;
    cin >> n;
    cout << n * n << endl;
    return 0;
}
"""

submission = prepare_submission(code)
status = compilers.cpp.run(submission)

assert submission.tests[0].returncode==0 , f"Execution error: {submission.tests[0].returncode}"
assert int(submission.tests[0].result) == 121, f"Expected 121, got {submission.tests[0].result}"
