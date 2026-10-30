# ESIT | worker
## Evaluation of algorythmic solutions
ESIT uses [nsjail](https://github.com/google/nsjail) to isolate algorithmic solutions and uses default mechanisms of compilers or OS to measure the parameters of compilation. Currently, it supports **python** (3.13), **C++** (C++20) and **Java** (OpenJDK 25).

## Manual start
To start the worker without running entire [compose.yaml](../compose.yaml) You should prepare proper environment by running worker's [dockerfile](../setup/worker-dockerfile).  

When proper environment is ready (make sure, that IP of your worker is on the proper list - You can add IPs in GUI) You can start worker using:
```bash
python3 /worker/mq_receiver.py
```
That's it.

## Worker's logs and config file
There are some variables that can be adjusted in [worker's config file](api/config/config.py) (however changing [.env file](/setup/.env.example) is recommended instead) - **mainly - the network key, that is necessary to successfully connect to web instance**.

Worker also stores simple logs in `logs/worker.log`.

## Tests suite
Worker is provided with tests suite by default and it automatically stops when any of the mentioned tests is failed. You can bypass this behavior by running `main()` of `mq_receiver.py` with `EnforcedTesting` set to `False` instead of `True`.

> [!WARNING]
> Tests suite is still under construction - please, consider the fact that passing these tests does not guarantee that your instance is completely secure. There is no such thing as a 100% secure system.

