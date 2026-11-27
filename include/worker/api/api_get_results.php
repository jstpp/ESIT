<?php

    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


function raise_error_and_die(int $error = 400): void
{
    http_response_code($error);
    die;
}

function set_system_error(array $sm): array
{
    $sm['result'] = "<-systemerror->";
    $sm['anws_correct'] = 0;
    $sm['anws_wrong'] = 0;
    $sm['anws_resource'] = 0;
    $sm['anws_syserror'] = 1;
    $sm['exec_time'] = 0;
    $sm['returncode'] = 255;
    $sm['comment'] = "System error";
    return $sm;
}

function save_test_result($sm, $submission, $pdo): void
{
    global $anws_correct, $anws_resource, $anws_wrong, $anws_syserror;
    $anws_correct += ($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] + $sm['anws_syserror'] != 0) ? $sm['anws_correct'] / ($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] + $sm['anws_syserror']) * $sm['weight'] : 0;
    $anws_resource += ($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] + $sm['anws_syserror'] != 0) ? $sm['anws_resource'] / ($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] + $sm['anws_syserror']) * $sm['weight'] : 0;
    $anws_wrong += ($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] + $sm['anws_syserror'] != 0) ? $sm['anws_wrong'] / ($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] + $sm['anws_syserror']) * $sm['weight'] : 0;
    $anws_syserror += ($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] + $sm['anws_syserror'] != 0) ? $sm['anws_syserror'] / ($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] + $sm['anws_syserror']) * $sm['weight'] : 0;

    if (!isset($sm['comment'])) {
        $sm['comment'] = "OK";
    }

    $db_query = $pdo->prepare('INSERT INTO RESULTS (submission_id, test_id, content, time, memory, comment, returncode, anws_correct, anws_wrong, anws_resource, anws_syserror) VALUES (:sid, :tid, :content, :time, :memory, :comment, :returncode, :ac, :aw, :ar, :ae)');
    $db_query->execute(['sid' => $submission['submission_id'], 'tid' => $sm['TEST_ID'], 'content' => $sm['result'], 'time' => $sm['exec_time'], 'memory' => $sm['memory'] ?? null, 'comment' => $sm['comment'], 'returncode' => $sm['returncode'], 'ac' => $sm['anws_correct'], 'aw' => $sm['anws_wrong'], 'ar' => $sm['anws_resource'], 'ae' => $sm['anws_syserror']]);
}

function save_submission_results($submission, $anws_correct, $anws_wrong, $anws_resource, $anws_syserror, $pdo): void
{
    $percentage = ($anws_correct + $anws_wrong + $anws_resource + $anws_syserror != 0) ? $anws_correct / ($anws_correct + $anws_wrong + $anws_resource + $anws_syserror) : 0;

    $notification_content = __("Your solution for problem") . " #" . $submission['problem_id'] . " " . __("has been evaluated") . "!<br/><a href='index.php?p=mysolutions'><i class='fa fa-eye'></i>&nbsp;" . __("My solutions") . "</a>";

    $db_query = $pdo->prepare('UPDATE SUBMISSIONS SET verification_time=CURRENT_TIMESTAMP, score=(:percentage1*(SELECT DISTINCT maxpoints FROM CONTENT WHERE CONTENT_ID=:pid)), score_percentage=(:percentage2*100) WHERE SUBMISSION_ID=:sid');
    $db_query->execute(['sid' => $submission['submission_id'], "percentage1" => $percentage, "percentage2" => $percentage, "pid" => $submission['problem_id']]);

    $db_query = $pdo->prepare('INSERT INTO NOTIFICATIONS (user_id, content, type) VALUES (:uid, :content, "success")');
    $db_query->execute(['uid' => $submission['user_id'], 'content' => $notification_content]);
}

function decrypt_payload($data, $tag, $worker_network_private_key): array
{
    $decrypted = openssl_decrypt($data['content'], 'AES-128-GCM', $worker_network_private_key, 0, base64_decode($data['nonce']), $tag);
    if ($decrypted === false) {
        raise_error_and_die();
    }
    $submission = json_decode($decrypted, true);
    if (
        !isset($submission['submission_id'], $submission['problem_id'], $submission['user_id']) ||
        filter_var($submission['submission_id'], FILTER_VALIDATE_INT) === false ||
        filter_var($submission['problem_id'], FILTER_VALIDATE_INT) === false ||
        (int)$submission['submission_id'] <= 0 ||
        (int)$submission['problem_id'] <= 0
    ) {
        raise_error_and_die();
    }
    return $submission;
}

function test_is_valid($sm): bool
{
    return isset(
        $sm['TEST_ID'],
        $sm['weight']
    ) && filter_var($sm['TEST_ID'], FILTER_VALIDATE_INT) > 0 && is_numeric($sm['weight']);
}

function evaluate($submission, $pdo): void
{
    foreach ($submission['tests'] as $sm) {
        if (
            $submission['status'] == "fail"
            || ($sm['returncode'] ?? 255) == 255
            || !test_is_valid($sm)
        ) {
            $sm = set_system_error($sm);
            save_test_result($sm, $submission, $pdo);
            continue;
        } else {
            $timefile = fopen(__DIR__ . "/../solutions/" . $submission['submission_id'] . "/time/" . $sm['TEST_ID'] . ".log", 'r');
            $checkfile = fopen(__DIR__ . "/../alg/" . $submission['problem_id'] . "/out/" . $sm['TEST_ID'] . ".out", 'r');

            if ($timefile === false || $checkfile === false) {
                if ($timefile !== false) {
                    fclose($timefile);
                }
                if ($checkfile !== false) {
                    fclose($checkfile);
                }
                $sm = set_system_error($sm);
                save_test_result($sm, $submission, $pdo);
                continue;
            } else {
                $time_data_array = array();
                if (!feof($timefile)) {
                    $sm['exec_time'] = fgets($timefile);
                    if (!str_starts_with($sm['exec_time'], "C")) {
                        array_push($time_data_array, $sm['exec_time']);
                    }
                }
                fclose($timefile);
            }

            switch ($sm['returncode']) {
                case 137:
                    $sm['result'] = "<-resource->";
                    break;
                case 1:
                    $sm['result'] = "<-error->";
                    break;
                default:
                    $result_file = __DIR__ . "/../solutions/" . $submission['submission_id'] . "/output/" . $sm['TEST_ID'] . ".log";
                    if (is_file($result_file)) {
                        if (filesize(__DIR__ . "/../solutions/" . $submission['submission_id'] . "/output/" . $sm['TEST_ID'] . ".log") <= 1024 * 1024 * 2) { # 2 MB
                            $sm['result'] = file_get_contents($result_file);
                            $solution_array = preg_split('/\r\n|\r|\n/', $sm['result'] ?? "");
                        } else {
                            $sm['comment'] = "Output file too large";
                            $sm['result'] = "<-resource->";
                            $sm['returncode'] = 137;
                        }
                    } else {
                        $sm = set_system_error($sm);
                        save_test_result($sm, $submission, $pdo);
                        fclose($checkfile);
                        continue 2;
                    }
                    break;
            }

            $i = 0;
            $sm['anws_correct'] = 0;
            $sm['anws_wrong'] = 0;
            $sm['anws_resource'] = 0;
            $sm['anws_syserror'] = 0;

            while (($correctanswer = fgets($checkfile)) !== false) {
                if (feof($checkfile) && trim($correctanswer) === "") {
                    break;
                }

                switch ($sm['returncode']) {
                    case 255:
                        $sm['comment'] = "System error";
                        $sm['anws_syserror']++;
                        break;
                    case 137:
                        if (!isset($sm['comment']) || (isset($sm['comment']) && strlen($sm['comment']) < 2)) {
                            $sm['comment'] = "Resource limit exceeded";
                        }
                        $sm['anws_resource']++;
                        break;
                    case 1:
                        $sm['comment'] = "Execution error";
                        $sm['anws_wrong']++;
                        break;
                    default:
                        $answer = ($i <= count($solution_array) - 1) ? $solution_array[$i] : "";
                        if (trim($correctanswer) === trim($answer)) {
                            $sm['anws_correct']++;
                        } else {
                            $sm['comment'] = "Received <code>" . htmlentities(preg_replace('/\s+/', '', $answer)) . "</code> when <code>" . htmlentities(preg_replace('/\s+/', '', $correctanswer)) . "</code> was required";
                            $sm['anws_wrong']++;
                        }
                }
                $i++;
            }
            fclose($checkfile);
            if ($sm['returncode'] === 0 && count(array_values(array_filter($solution_array, fn($line) => trim($line) !== ''))) > $i) 
            {
                $sm['anws_wrong'] += count(array_values(array_filter($solution_array, fn($line) => trim($line) !== ''))) - $i;
                $sm['comment'] = "Extra output lines; " . ($sm['comment'] ?? '');
            }
        }
        save_test_result($sm, $submission, $pdo);
    }
}

function setup_filesystem($submission): void
{
    $binary_data = base64_decode($submission['submission_file']);
    if ($binary_data === false || strlen($binary_data) > 10 * 1024 * 1024) {
        raise_error_and_die();
    }

    file_put_contents(__DIR__ . "/../solutions/" . $submission['submission_id'] . "/" . $submission['submission_id'] . "_result.zip", $binary_data);
    $zip = new ZipArchive();
    if (!extract_zip(__DIR__ . "/../solutions/" . $submission['submission_id'] . "/" . $submission['submission_id'] . "_result.zip", __DIR__ . "/../solutions/" . $submission['submission_id'] . "/")) {
        unlink(__DIR__ . "/../solutions/" . $submission['submission_id'] . "/" . $submission['submission_id'] . "_result.zip");
        raise_error_and_die();
    }
}

try {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!is_array($data) || !isset($data['tag'], $data['nonce'], $data['content'])) {
        raise_error_and_die();
    }
    $tag = base64_decode($data['tag']);

    try {
        $submission = decrypt_payload($data, $tag, $worker_network_private_key);
        setup_filesystem($submission);
    } catch (Throwable $t) {
        extended_exception_handler($t);
        raise_error_and_die();
    }

    $anws_correct = 0;
    $anws_wrong = 0;
    $anws_resource = 0;
    $anws_syserror = 0;

    try {
        evaluate($submission, $pdo);
    } catch (Throwable $t) {
        extended_exception_handler($t);
        raise_error_and_die();
    }

    try {
        save_submission_results($submission, $anws_correct, $anws_wrong, $anws_resource, $anws_syserror, $pdo);
    } catch (Throwable $t) {
        extended_exception_handler($t);
        raise_error_and_die();
    }

    http_response_code(200);
} catch (Throwable $t) {
    extended_exception_handler($t);
    raise_error_and_die();
}
