import base64
import io
import os
import tempfile
import zipfile

from api import lib


def make_zip(filename, content):
    buffer = io.BytesIO()
    with zipfile.ZipFile(buffer, "w") as archive:
        archive.writestr(filename, content)
    return base64.b64encode(buffer.getvalue()).decode("utf-8")


with tempfile.TemporaryDirectory() as tmp:
    output_dir = os.path.join(tmp, "output")
    os.makedirs(output_dir)

    payload = make_zip("../outside.txt", "must not be written")
    result = lib.decode_and_extract_zip(payload, output_dir)

    assert result is False, "Zip Slip archive should be rejected"
    assert not os.path.exists(os.path.join(tmp, "outside.txt")), (
        "Zip Slip archive wrote a file outside the target directory"
    )
