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
        if ($zip->open(__DIR__."/../solutions/".$submission['submission_id']."/".$submission['submission_id']."_result.zip") === TRUE) {
            $zip->extractTo(__DIR__."/../solutions/".$submission['submission_id']."/");
            $zip->close();
        }

        $anws_correct = 0;
        $anws_wrong = 0;
        $anws_resource = 0;

        foreach($submission['tests'] as $sm)
        {
            $timefile = fopen(__DIR__."/../solutions/".$submission['submission_id']."/time/".$sm['TEST_ID'].".log",'r');
            $checkfile = fopen(__DIR__."/../alg/".$submission['problem_id']."/out/".$sm['TEST_ID'].".out",'r');
            
            $time_data_array = array();
            if(!feof($timefile))
            {
                $sm['exec_time'] = fgets($timefile);
                array_push($time_data_array, $sm['exec_time']);
            }
            fclose($timefile);

            if(isset($sm['result']) and $sm['result']!="")
            {
                $solution_array = preg_split('/\r\n|\r|\n/', $sm['result']);
            } else {
                if (!preg_match('/status (\d+)/', $time_data_array[0], $matches)) {
                    $sm['result'] = "<-error->";
                } else {
                    switch((int)$matches[1])
                    {
                        case 1:
                            $sm['result'] = "<-error->";
                            break;
                        case 137:
                            $sm['result'] = "<-resource->";
                            break;
                        case 255:
                            $sm['result'] = "<-systemerror->";
                            break;
                    }
                }
                $solution_array = preg_split('/\r\n|\r|\n/', $sm['result']);
            }

            $i = 0;
            $sm['anws_correct'] = 0;
            $sm['anws_wrong'] = 0;
            $sm['anws_resource'] = 0;

            while(!feof($checkfile))
            {
                $correctanwser = fgets($checkfile);
                $anwser = ($i<=count($solution_array)-1) ? $solution_array[$i] : "";

                if(preg_replace('/\s+/', '', $correctanwser)==preg_replace('/\s+/', '',$anwser))
                {
                    if((float)$sm['max_time']<(float)$sm['exec_time'])
                    {
                        #$anws_resource++;
                        $sm['anws_resource']++;
                        $sm['comment'] = "Przekroczono limit czasu";
                    } else {
                        #$anws_correct++;
                        $sm['anws_correct']++;
                    }
                } else {
                    if((float)$sm['max_time']<(float)$sm['exec_time'])
                    {
                        #$anws_resource++;
                        $sm['anws_resource']++;
                        $sm['comment'] = "Przekroczono limit czasu";
                    } else {
                        if($sm['result']!="<-error->" 
                        and $sm['result']!="<-resource->" 
                        and $sm['result']!="<-systemerror->")
                        {
                            if(!isset($sm['comment'])) $sm['comment'] = "Otrzymano <code>".htmlentities(preg_replace('/\s+/', '',$anwser))."</code> a oczekiwano <code>".htmlentities(preg_replace('/\s+/', '', $correctanwser))."</code> (...)";
                            #$anws_wrong++;
                            $sm['anws_wrong']++;
                        } else if ($sm['result']=="<-error->")
                        {
                            $sm['comment'] = "Błąd kompilacji";
                            #$anws_wrong++;
                            $sm['anws_wrong']++;
                        } else if ($sm['result']=="<-resource->")
                        {
                            $sm['comment'] = "Przekroczono limit pamięci";
                            #$anws_resource++;
                            $sm['anws_resource']++;
                        } else if ($sm['result']=="<-systemerror->")
                        {
                            $sm['comment'] = "Błąd systemu";
                            #$anws_resource++;
                            $sm['anws_resource']++;
                        }
                    }
                }
                $i++;
            }
            fclose($checkfile);

            $anws_correct += ($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] != 0) ? $sm['anws_correct']/($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'])*$sm['weight'] : 0;
            $anws_resource += ($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] != 0) ? $sm['anws_resource']/($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'])*$sm['weight'] : 0;
            $anws_wrong += ($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'] != 0) ? $sm['anws_wrong']/($sm['anws_correct'] + $sm['anws_resource'] + $sm['anws_wrong'])*$sm['weight'] : 0;

            if(!isset($sm['comment'])) $sm['comment'] = "OK";
            $db_query = $pdo->prepare('INSERT INTO RESULTS (submission_id, test_id, content, time, memory, comment, anws_correct, anws_wrong, anws_resource) VALUES (:sid, :tid, :content, :time, :memory, :comment, :ac, :aw, :ar)');
            $db_query->execute(['sid' => $submission['submission_id'], 'tid' => $sm['TEST_ID'], 'content' => $sm['result'], 'time' => $sm['exec_time'], 'memory' => $sm['memory'] ?? null, 'comment' => $sm['comment'], 'ac' => $sm['anws_correct'], 'aw' => $sm['anws_wrong'], 'ar' => $sm['anws_resource']]);
        }

        $percentage = ($anws_correct+$anws_wrong+$anws_resource!=0) ? $anws_correct/($anws_correct+$anws_wrong+$anws_resource) : 0;

        $notification_content = "Twoje rozwiązanie do zadania #".$submission['problem_id']." zostało sprawdzone!<br/><a href='index.php?p=mysolutions'><i class='fa fa-eye'></i>&nbsp;Moje rozwiązania</a>";

        $db_query = $pdo->prepare('UPDATE SUBMISSIONS SET verification_time=CURRENT_TIMESTAMP, score=(:percentage1*(SELECT DISTINCT maxpoints FROM PROBLEMS WHERE PROBLEM_ID=:pid)), score_percentage=(:percentage2*100) WHERE SUBMISSION_ID=:sid');
        $db_query->execute(['sid' => $submission['submission_id'], "percentage1" => $percentage, "percentage2" => $percentage, "pid" => $submission['problem_id']]);

        $db_query = $pdo->prepare('INSERT INTO NOTIFICATIONS (user_id, content, type) VALUES (:uid, :content, "success")');
        $db_query->execute(['uid' => $submission['user_id'], 'content' => $notification_content]);
    } catch (Throwable $t) {
        extended_exception_handler($t);
        die;
    }
?>