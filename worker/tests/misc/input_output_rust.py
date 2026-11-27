import base64
import os
import shutil

import compilers.rust
from obj.submission import Submission


def prepare_submission(code):
    base_dir = os.path.abspath(
        os.path.join(os.path.dirname(os.path.realpath(__file__)), "..")
    )
    tmp_dir = os.path.join(base_dir, "tmp")
    inout_dir = os.path.join(tmp_dir, "0-inout")
    run_dir = os.path.join(tmp_dir, "0")

    os.makedirs(tmp_dir, exist_ok=True)

    if not os.path.exists(inout_dir):
        os.makedirs(os.path.join(inout_dir, "in"), exist_ok=True)
        os.makedirs(os.path.join(inout_dir, "out"), exist_ok=True)
        with open(os.path.join(inout_dir, "in", "0.in"), "w", encoding="utf-8") as f:
            f.write("11")
        with open(os.path.join(inout_dir, "out", "0.out"), "w", encoding="utf-8") as f:
            f.write("121")

    if os.path.exists(run_dir):
        shutil.rmtree(run_dir)

    for sub in ("code", "misc", "time", "output"):
        os.makedirs(os.path.join(run_dir, sub), exist_ok=True)

    with open(os.path.join(run_dir, "code", "0.rs"), "w", encoding="utf-8") as f:
        f.write(code)
    with open(os.path.join(run_dir, "misc", "debug-compilation.log"), "w", encoding="utf-8") as f:
        f.write("")

    submission = {
        "submission_id": "0",
        "submission_type": "testing",
        "user_id": 0,
        "problem_id": 0,
        "submission_lang": "rs",
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


code = """use std::io::{self, Read};

fn main() {
    let mut input = String::new();
    io::stdin().read_to_string(&mut input).unwrap();
    let n: i64 = input.trim().parse().unwrap();
    println!("{}", n * n);
}
"""

submission = prepare_submission(code)
status = compilers.rust.run(submission)

assert status == "success", f"Worker failed with status: {status}"
assert submission.tests[0].returncode == 0, f"Execution error: {submission.tests[0].returncode}"
assert int(submission.tests[0].result.strip()) == 121, f"Expected 121, got {submission.tests[0].result}"