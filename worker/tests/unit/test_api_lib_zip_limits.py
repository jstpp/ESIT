import base64
import io
import os
import tempfile
import zipfile

from api import lib


def make_zip(entries):
    buffer = io.BytesIO()
    with zipfile.ZipFile(buffer, "w") as archive:
        for filename, content in entries:
            archive.writestr(filename, content)
    return base64.b64encode(buffer.getvalue()).decode("utf-8")


with tempfile.TemporaryDirectory() as tmp:
    output_dir = os.path.join(tmp, "output")
    os.makedirs(output_dir)

    payload = make_zip([
        ("one.txt", "1"),
        ("two.txt", "2"),
    ])
    result = lib.decode_and_extract_zip(
        payload,
        output_dir,
        max_files=1,
    )
    assert result is False, "Archive exceeding max_files should be rejected"

with tempfile.TemporaryDirectory() as tmp:
    output_dir = os.path.join(tmp, "output")
    os.makedirs(output_dir)

    payload = make_zip([
        ("large.txt", "A" * 1024),
    ])
    result = lib.decode_and_extract_zip(
        payload,
        output_dir,
        max_uncompressed_size=100,
    )
    assert result is False, (
        "Archive exceeding max_uncompressed_size should be rejected"
    )

with tempfile.TemporaryDirectory() as tmp:
    output_dir = os.path.join(tmp, "output")
    os.makedirs(output_dir)

    payload = make_zip([("small.txt", "OK")])
    result = lib.decode_and_extract_zip(
        payload,
        output_dir,
        max_zip_size=1,
    )
    assert result is False, "Archive exceeding max_zip_size should be rejected"
