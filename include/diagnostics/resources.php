<?php
    function getMemory() {
        $mem = [];
        if(is_readable('/proc/meminfo')){
            $content = file_get_contents('/proc/meminfo');
            preg_match_all('/(\w+):\s+(\d+)\s/', $content, $matches);
            $mem = array_combine($matches[1], $matches[2]);
        }
        return $mem;
    }

    function getNumOfCPUs() {
        $CPUs = -1;
        if(is_file('/proc/cpuinfo')) {
            $cpufile = file_get_contents('/proc/cpuinfo');
            preg_match_all('/^processor/m', $cpufile, $matches);
            $CPUs = count($matches[0]);
        }
        return $CPUs;
    }

    function getCPUUsage() {
        return sys_getloadavg()[0];
    }

    if (isset($_GET['call']) and has_a_priority(3))
    {
        if ($_GET['call']=="getall") {
            echo json_encode([
                'success' => true,
                'value' => [
                    'memory' => getMemory(),
                    'cpus' => getNumOfCPUs(),
                    'cpuusage' => getCPUUsage()
                ]
            ]);
        }
        else if($_GET['call']=="getmemory")
        {
            echo json_encode([
                'success' => true,
                'value' => getMemory()
            ]);
        } 
        else if ($_GET['call']=="getnumofcpus") {
            echo json_encode([
                'success' => true,
                'value' => getNumOfCPUs()
            ]);
        }
        else if ($_GET['call']=="c") {
            echo json_encode([
                'success' => true,
                'value' => getCPUUsage()
            ]);
        }
    }
?>