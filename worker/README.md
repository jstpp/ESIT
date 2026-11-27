# ESIT | worker
## Evaluation of algorythmic solutions
ESIT uses [nsjail](https://github.com/google/nsjail) to isolate algorithmic solutions and uses default mechanisms of compilers or OS to measure the parameters of compilation. Currently, it supports **python** (3.13), **C++** (C++20), **Rust** and **Java** (OpenJDK 25).

## Manual start
To start the worker without running entire [compose.yaml](../compose.yaml) You should prepare proper environment by running worker's [dockerfile](../setup/worker-dockerfile).  

When proper environment is ready (make sure, that IP of your worker is on the proper list - You can add IPs in GUI) You can start worker using:
```bash
python3 /worker/mq_receiver.py 1
```
The last argument is a number of workers that will be started. Each worker is able to process only one job at once so consider adjusting workers' number to Your needs.

That's it.

## Worker's logs
Worker stores simple logs in `logs/` directory.

## Tests suite
Worker is provided with tests suite by default and it automatically stops when any of the mentioned tests is failed. You can bypass this behavior by running `main()` of `mq_receiver.py` with `ENFORCED_TESTING` set to `False` instead of `True`.

> [!WARNING]
> Tests suite is still under construction - please, consider the fact that passing these tests does not guarantee that your instance is completely secure. There is no such thing as a 100% secure system.

## Details
### Submission message in JSON (app -> worker)
```json
{
  "submission_id": 123,
  "submission_type": "normal",
  "user_id": "550e8400-e29b-41d4-a716-446655440000",
  "problem_id": 42,
  "submission_lang": "py",
  "tests": [
    {
      "TEST_ID": 101,
      "test_author_id": "550e8400-e29b-41d4-a716-446655440001",
      "problem_id": 42,
      "max_time": 2.0,
      "max_memory": 256,
      "weight": 10
    },
    {
      "TEST_ID": 102,
      "test_author_id": "550e8400-e29b-41d4-a716-446655440001",
      "problem_id": 42,
      "max_time": 2.0,
      "max_memory": 256,
      "weight": 20
    }
  ],
  "listenerUrl": "https://esit.example.org",
  "submission_time": "2026-11-20 10:45:00",
  "submission_file": "<BASE64_ENCODED_ZIP_ARCHIVE>"
}
```

### Response in JSON (worker -> app)

```json
{
  "submission_id": 123,
  "submission_type": "normal",
  "user_id": "550e8400-e29b-41d4-a716-446655440000",
  "problem_id": 42,
  "submission_lang": "py",
  "tests": [
    {
      "TEST_ID": 101,
      "test_author_id": "550e8400-e29b-41d4-a716-446655440001",
      "problem_id": 42,
      "max_time": 2.0,
      "max_memory": 256,
      "weight": 10,
      "result": "Hello, world!\n",
      "returncode": 0
    },
    {
      "TEST_ID": 102,
      "test_author_id": "550e8400-e29b-41d4-a716-446655440001",
      "problem_id": 42,
      "max_time": 2.0,
      "max_memory": 256,
      "weight": 20,
      "result": "42\n",
      "returncode": 0
    }
  ],
  "listenerUrl": "https://esit.example.org",
  "submission_time": "2026-11-20 10:45:00",
  "submission_file": "<BASE64_ENCODED_RESULT_ZIP_ARCHIVE>",
  "status": "success"
}
```