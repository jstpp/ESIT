from dataclasses import asdict, dataclass
from datetime import datetime
from typing import Literal


SubmissionLanguage = Literal["py", "cpp", "java"]
SubmissionType = Literal["normal", "silent", "recheck", "testing"]


@dataclass(slots=True)
class Test:
    TEST_ID: int
    test_author_id: str
    problem_id: int
    max_time: float
    max_memory: int
    weight: int

    result: str | None = None
    returncode: int | None = None

    def to_dict(self) -> dict:
        return asdict(self)


@dataclass(slots=True)
class Submission:
    submission_id: int
    submission_type: SubmissionType
    user_id: str
    problem_id: int
    submission_lang: SubmissionLanguage
    tests: list[Test]
    listenerUrl: str
    submission_time: datetime
    submission_file: str

    @classmethod
    def from_dict(cls, data: dict):
        if not isinstance(data, dict):
            raise ValueError("Submission must be a JSON object")

        tests = [
            Test(
                TEST_ID=int(test["TEST_ID"]),
                test_author_id=str(test["test_author_id"]),
                problem_id=int(test["problem_id"]),
                max_time=float(test["max_time"]),
                max_memory=int(test["max_memory"]),
                weight=int(test["weight"]),
                result=test.get("result"),
                returncode=test.get("returncode"),
            )
            for test in data["tests"]
        ]
        return Submission(
            submission_id=int(data["submission_id"]),
            submission_type=data["submission_type"],
            user_id=data["user_id"],
            problem_id=int(data["problem_id"]),
            submission_lang=data["submission_lang"],
            tests=tests,
            listenerUrl=data["listenerUrl"],
            submission_time=datetime.strptime(
                data["submission_time"],
                "%Y-%m-%d %H:%M:%S",
            ),
            submission_file=data["submission_file"],
        )

    def to_dict(self) -> dict:
        data = asdict(self)
        data["submission_time"] = self.submission_time.strftime(
            "%Y-%m-%d %H:%M:%S"
        )
        return data

    def __post_init__(self):
        if self.submission_id < 0:
            raise ValueError("Invalid submission_id")

        if self.problem_id < 0:
            raise ValueError("Invalid problem_id")

        if self.submission_lang not in {"py", "cpp", "java"}:
            raise ValueError(f"Unsupported submission language: {self.submission_lang}")

        if self.submission_type not in {"normal", "silent", "recheck", "testing"}:
            raise ValueError(f"Invalid submission type: {self.submission_type}")

        if not self.tests:
            raise ValueError("Submission has no tests")