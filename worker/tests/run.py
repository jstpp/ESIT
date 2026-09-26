import time, os, subprocess, sys

class clrs:
    PINK = '\033[95m'
    BLUE = '\033[94m'
    CYAN = '\033[96m'
    GREEN = '\033[92m'
    YELLOW = '\033[93m'
    RED = '\033[91m'
    ENDC = '\033[0m'
    BOLD = '\033[1m'
    UNDERLINE = '\033[4m'


def run_test(path, env):
    result = subprocess.run(
        [sys.executable, path],
        capture_output=True,
        text=True,
        env=env
    )

    if result.returncode != 0:
        raise RuntimeError(result.stderr.strip())

    return result.stdout.strip()

def security_tests(quiet = False):
    env = os.environ.copy()
    env["PYTHONPATH"] = os.path.abspath(os.path.dirname(__file__) + "/../")
    if not quiet:
        print(str(time.ctime())+ " | " + f"[ * ] Running security tests")
    ok,warn,err = 0,0,0
    for f in os.scandir(os.path.dirname(__file__) + '/security/'):
        if f.is_file():
            if not quiet:
                print(str(time.ctime())+ " | " + f"  ├── Running: {clrs.PINK}" + f.path + f"{clrs.ENDC}")
            try:
                run_test(f.path, env)
                if not quiet:
                    print(str(time.ctime())+ " | " + f"  ├── Status:  {clrs.GREEN}OK{clrs.ENDC}")
                ok += 1
            except Exception as e:
                if not quiet:
                    print(str(time.ctime()) + " |   ├── Status:  " + f"{clrs.RED}FAILED{clrs.ENDC}")
                    print(str(time.ctime()) + " |   ├── Reason:")
                    for line in str(e).splitlines():
                        print(str(time.ctime()) + f" |   │   {clrs.RED}" + line + f"{clrs.ENDC}")
                err += 1

    return [ok,warn,err]

def unit_tests(quiet = False):
    env = os.environ.copy()
    env["PYTHONPATH"] = os.path.abspath(os.path.dirname(__file__) + "/../")

    if not quiet:
        print(str(time.ctime())+ " | " + f"[ * ] Running unit tests")
    ok,warn,err = 0,0,0
    for f in os.scandir(os.path.dirname(__file__) + '/unit/'):
        if f.is_file():
            if not quiet:
                print(str(time.ctime())+ " | " + f"  ├── Running: {clrs.PINK}" + f.path + f"{clrs.ENDC}")
            try:
                if not quiet:
                    print(str(time.ctime())+ " | " + f"  ├── Status:  {clrs.GREEN}OK{clrs.ENDC}")
                ok += 1
            except Exception as e:
                if not quiet:
                    print(str(time.ctime()) + " |   ├── Status:  " + f"{clrs.RED}FAILED{clrs.ENDC}")
                    print(str(time.ctime()) + " |   ├── Reason:")
                    for line in str(e).splitlines():
                        print(str(time.ctime()) + f" |   │   {clrs.RED}" + line + f"{clrs.ENDC}")
                err += 1

    return [ok,warn,err]

def is_ok(quiet = False):
    if not quiet:
        print("\n" + str(time.ctime())+ " | " + f"{clrs.BOLD}███████████████████████  RUNNING TESTS  █████████████████████████{clrs.ENDC}\n")

    ok,warn,err = 0,0,0

    unit_tests_result = unit_tests(quiet)
    security_tests_result = security_tests(quiet)

    if not quiet:
        print(str(time.ctime())+ " | " + f"[ * ] Testing finished")

    ok = unit_tests_result[0] + security_tests_result[0]
    warn = unit_tests_result[1] + security_tests_result[1]
    err = unit_tests_result[2] + security_tests_result[2]

    if not quiet:
        print("\n" + str(time.ctime())+ " | " + f"{clrs.BOLD}███████████████████  {clrs.GREEN}OK: {ok}  {clrs.YELLOW}WARN: {warn}  {clrs.RED}ERR: {err}{clrs.ENDC}  ████████████████████{clrs.ENDC}\n")

    if(err>0):
        return False
    else:
        return True