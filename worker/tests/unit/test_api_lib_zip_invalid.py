import os
import tempfile

from api import lib


with tempfile.TemporaryDirectory() as tmp:
    result = lib.decode_and_extract_zip(
        "this-is-not-valid-base64!!!",
        tmp,
    )
    assert result is False, "Invalid Base64 input should be rejected"

with tempfile.TemporaryDirectory() as tmp:
    result = lib.decode_and_extract_zip(
        "bm90IGEgemlwIGFyY2hpdmU=",  # "not a zip archive"
        tmp,
    )
    assert result is False, "Invalid ZIP archive should be rejected"
