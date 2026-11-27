<?php

    include_once(__DIR__ . "/../resources.php");

    # getMemory():
try {
    $temp = getMemory();
} catch (Throwable $t) {
    extended_exception_handler($t);
    array_push($results, [
        "category" => "error",
        "content" => "getMemory() doesn't work as expected!"
    ]);
}

    # getNumOfCPUs():
try {
    $temp = getNumOfCPUs();
    if (filter_var($temp, FILTER_VALIDATE_INT) <= 0) {
        throw new Exception();
    }
} catch (Throwable $t) {
    extended_exception_handler($t);
    array_push($results, [
        "category" => "error",
        "content" => "getNumOfCPUs() doesn't work as expected!"
    ]);
}

    # getCPUUsage():
try {
    $temp = getCPUUsage();
    if (filter_var($temp, FILTER_VALIDATE_INT) < 0) {
        throw new Exception();
    }
} catch (Throwable $t) {
    extended_exception_handler($t);
    array_push($results, [
        "category" => "error",
        "content" => "getCPUUsage() doesn't work as expected!"
    ]);
}
