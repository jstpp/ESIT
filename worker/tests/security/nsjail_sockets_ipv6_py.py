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
import socket

targets = [
    ("2606:4700:4700::1111", 443),       # Cloudflare
    ("2001:4860:4860::8888", 53),        # Google DNS
    ("::1", 80),                         # localhost IPv6
]

t = 0

for host, port in targets:
    try:
        with socket.socket(socket.AF_INET6, socket.SOCK_STREAM) as s:
            s.settimeout(2)
            s.connect((host, port))

        print(f"[FAIL] IPv6 connection succeeded: [{host}]:{port}")

    except OSError as e:
        print(f"[OK] IPv6 blocked: [{host}]:{port}: {type(e).__name__}")
        t += 1

if t != len(targets):
    raise Exception()
    
"""

submission = prepare_submission(code)
status = compilers.python.run(submission)

assert submission["tests"][0]["returncode"] == 0, submission["tests"][0]["returncode"]
