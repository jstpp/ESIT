<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    http_response_code(200);

    try {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        $tag = base64_decode($data['tag']);

        $submission = json_decode(openssl_decrypt($data['content'], 'AES-128-GCM', $worker_network_private_key, $options=0, base64_decode($data['nonce']), $tag), true);
        
        
        $binaryData = base64_decode($submission['submission_file']);
        file_put_contents(__DIR__."/../solutions/".$submission['submission_id']."/".$submission['submission_id']."_result.zip", $binaryData);
        
        $zip = new ZipArchive;
        if(!extract_zip(__DIR__."/../solutions/".$submission['submission_id']."/".$submission['submission_id']."_result.zip", __DIR__."/../solutions/".$submission['submission_id']."/"))
        {
            unlink(__DIR__."/../solutions/".$submission['submission_id']."/".$submission['submission_id']."_result.zip");
        }

        $anws_correct = 0;
        $anws_wrong = 0;
        $anws_resource = 0;
        $anws_syserror = 0;

        foreach($submission['tests'] as $sm)
        {
            if ($submission['status']=="fail" || $sm['returncode']==255) {
                $sm['result'] = "<-systemerror->";
                $sm['anws_correct'] = 0;
                $sm['anws_wrong'] = 0;
                $sm['anws_resource'] = 0;
                $sm['anws_syserror'] = 1;
                $sm['exec_time'] = 0;
                $sm['comment'] = "System error";
            } else {
                $timefile = fopen(__DIR__."/../solutions/".$submission['submission_id']."/time/".$sm['TEST_ID'].".log",'r');
                $checkfile = fopen(__DIR__."/../alg/".$submission['problem_id']."/out/".$sm['TEST_ID'].".out",'r');
                
                $time_data_array = array();
                if(!feof($timefile))
                {
                    $sm['exec_time'] = fgets($timefile);
                    if(!str_starts_with($sm['exec_time'], "C"))
                    {
                        array_push($time_data_array, $sm['exec_time']);
                    }
                }
                fclose($timefile);

                switch($sm['returncode'])
                {
                    case 137:
                        $sm['result'] = "<-resource->";
                        break;
                    case 1:
                        $sm['result'] = "<-error->";
                        break;
                    default:
                        $solution_array = preg_split('/\r\n|\r|\n/', $sm['result']);
                }

                $i = 0;
                $sm['anws_correct'] = 0;
                $sm['anws_wrong'] = 0;
                $sm['anws_resource'] = 0;
                $sm['anws_syserror'] = 0;

                while(!feof($checkfile))
                {
                    $correctanwser = fgets($checkfile);
                    if(feof($checkfile) && trim($correctanwser) === "") break;

                    switch($sm['returncode'])
                    {
                        case 255:
                            $sm['comment'] = "System error";
                            $sm['anws_syserror']++;
                            break;
                        case 137:
                            $sm['comment'] = "Resource limit exceeded";
                            $sm['anws_resource']++;
                            break;
                        case 1:
                            $sm['comment'] = "Execution error";
                            $sm['anws_wrong']++;
                            break;
                        default:
                            $anwser = ($i<=count($solution_array)-1) ? $solution_array[$i] : "";
                            if(trim($correctanwser)==trim($anwser))
                            {
                                $sm['anws_correct']++;
                            } else {
                                $sm['comment'] = "Received <code>".htmlentities(preg_replace('/\s+/', '',$anwser))."</code> when <code>".htmlentities(preg_replace('/\s+/', '', $correctanwser))."</code> was required";
                                $sm['anws_wrong']++;
                            }
                    }
                    $i++;
                }
                fclose($checkfile);
            }
        

            $anws_correct += ($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] + $sm['anws_syserror'] != 0) ? $sm['anws_correct']/($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] + $sm['anws_syserror'])*$sm['weight'] : 0;
            $anws_resource += ($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] + $sm['anws_syserror'] != 0) ? $sm['anws_resource']/($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] + $sm['anws_syserror'])*$sm['weight'] : 0;
            $anws_wrong += ($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] + $sm['anws_syserror'] != 0) ? $sm['anws_wrong']/($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] + $sm['anws_syserror'])*$sm['weight'] : 0;
            $anws_syserror += ($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] + $sm['anws_syserror'] != 0) ? $sm['anws_syserror']/($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] + $sm['anws_syserror'])*$sm['weight'] : 0;

            if(!isset($sm['comment'])) $sm['comment'] = "OK";
            $db_query = $pdo->prepare('INSERT INTO RESULTS (submission_id, test_id, content, time, memory, comment, returncode, anws_correct, anws_wrong, anws_resource, anws_syserror) VALUES (:sid, :tid, :content, :time, :memory, :comment, :returncode, :ac, :aw, :ar, :ae)');
            $db_query->execute(['sid' => $submission['submission_id'], 'tid' => $sm['TEST_ID'], 'content' => $sm['result'], 'time' => $sm['exec_time'], 'memory' => $sm['memory'] ?? null, 'comment' => $sm['comment'], 'returncode' => $sm['returncode'], 'ac' => $sm['anws_correct'], 'aw' => $sm['anws_wrong'], 'ar' => $sm['anws_resource'], 'ae' => $sm['anws_syserror']]);
        }

        $percentage = ($anws_correct+$anws_wrong+$anws_resource+$anws_syserror!=0) ? $anws_correct/($anws_correct+$anws_wrong+$anws_resource+$anws_syserror) : 0;

        $notification_content = __("Your solution for problem")." #".$submission['problem_id']." ".__("has been evaluated")."!<br/><a href='index.php?p=mysolutions'><i class='fa fa-eye'></i>&nbsp;".__("My solutions")."</a>";

        $db_query = $pdo->prepare('UPDATE SUBMISSIONS SET verification_time=CURRENT_TIMESTAMP, score=(:percentage1*(SELECT DISTINCT maxpoints FROM CONTENT WHERE CONTENT_ID=:pid)), score_percentage=(:percentage2*100) WHERE SUBMISSION_ID=:sid');
        $db_query->execute(['sid' => $submission['submission_id'], "percentage1" => $percentage, "percentage2" => $percentage, "pid" => $submission['problem_id']]);

        $db_query = $pdo->prepare('INSERT INTO NOTIFICATIONS (user_id, content, type) VALUES (:uid, :content, "success")');
        $db_query->execute(['uid' => $submission['user_id'], 'content' => $notification_content]);
    } catch (Throwable $t) {
        extended_exception_handler($t);
        die;
    }
?>