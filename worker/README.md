# ESIT | worker
## Evaluation of algorythmic solutions
ESIT uses [nsjail](https://github.com/google/nsjail) to isolate algorythmic solutions and uses default mechanisms of compilers or OS to measure the parameters of compilation. Currently, it supports **python** (3.11) and **C++** (C++20).

## Manual start
To start the worker without running entire [compose.yaml](../compose.yaml) You should prepare proper environment by running worker's [dockerfile](../setup/worker-dockerfile). Take into consideration, that worker's container uses privileged mode to enable containerization (nsjail sandboxes). 
> [!tip] 
>
> When privileged mode of docker container is not enabled, worker will probably return `255` error code. It's probably related with docker's seccomp policy.

When proper environment is ready You can start worker using
```bash
python3 /worker/mq_receiver.py
```
That's all.

## Worker's logs and config file
There are some variables available to change in [worker's config file](api/config/config.py) - **mainly - the network key, that is necessary to successfully connect to web instance**.

Worker contains also simple logs in `logs/worker.log`.

