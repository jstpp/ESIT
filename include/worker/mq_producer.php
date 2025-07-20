<?php
    if(!is_logged_in()) kick();

    require_once('modules/composer/vendor/autoload.php');
	use PhpAmqpLib\Connection\AMQPStreamConnection;
	use PhpAmqpLib\Message\AMQPMessage;

    function savesubmission($submission_id, $content, $submission_lang)
	{
        try {
            if (!file_exists(__DIR__."/solutions/".$submission_id))
            {
                mkdir(__DIR__."/solutions/".$submission_id, 0777);
                mkdir(__DIR__."/solutions/".$submission_id."/code", 0777);
                mkdir(__DIR__."/solutions/".$submission_id."/misc", 0777);
                mkdir(__DIR__."/solutions/".$submission_id."/time", 0777);
                mkdir(__DIR__."/solutions/".$submission_id."/output", 0777);

                chmod(__DIR__."/solutions/".$submission_id, 0777);
                chmod(__DIR__."/solutions/".$submission_id."/code", 0777);
                chmod(__DIR__."/solutions/".$submission_id."/misc", 0777);
                chmod(__DIR__."/solutions/".$submission_id."/time", 0777);
                chmod(__DIR__."/solutions/".$submission_id."/output", 0777);

                $codefile = fopen(__DIR__."/solutions/".$submission_id."/code/".$submission_id.".".$submission_lang, "w");
                if(isset($_POST['sendtext'])) {
                    fwrite($codefile, $content);
                    fclose($codefile);
                    return true;
                } else {
                    die;
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
		
        return false;
	}

    function mqsend($mq_host, $mq_port, $mq_user, $mq_password, $data)
	{

		$connection = new AMQPStreamConnection($mq_host, $mq_port, $mq_user, $mq_password);
		$channel = $connection->channel();

		$msg = new AMQPMessage($data);
		$channel->basic_publish($msg, '', 'esit'); 

		$channel->close();
		$connection->close();
        echo "Connection closed successfully.";
        return true;
	}

    function zip_submission($submission_id)
    {
        $pathdir = __DIR__.'/solutions/'.$submission_id.'/'; 
        $zipcreated = __DIR__.'/solutions/'.$submission_id."/".$submission_id.".zip";
        $zip = new ZipArchive;

        if($zip -> open($zipcreated, ZipArchive::CREATE ) === TRUE) 
        {
            $files = scandir($pathdir);
            foreach ($files as $file) {
                if ($file == '.' || $file == '..') continue;
                $zip -> addEmptyDir($file);
                if(is_dir($pathdir.$file))
                {
                    $dir = opendir($pathdir.$file);
                    while($next_file = readdir($dir)) {
                        if ($next_file == '.' || $next_file == '..') continue;
                        if(is_file($pathdir.$file."/".$next_file)) {
                            $zip -> addFile($pathdir.$file."/".$next_file, $file."/".$next_file);
                        }
                    }
                }
            }
            $zip ->close();
        }
        return $zipcreated;
    }

    echo("Loading...");
    if(isset($_POST['lang']) and is_logged_in())
    {
        if($_POST['lang']=="cpp")
        {
            $submission_lang = "cpp";
        } else {
            $submission_lang = "py";
        }
    } else {
        kick();
    }

    $db_query = $pdo->prepare('INSERT INTO SUBMISSIONS (problem_id, problemset_id, user_id, verification_time, score, score_percentage, submission_lang) VALUES (:pid, (SELECT DISTINCT problemset FROM PROBLEMS WHERE PROBLEM_ID=:xpid), :uid, :ver_time, -1, -1, :sub_lang)');
    $db_query->execute(['pid' => $_GET['pid'], 'xpid' => $_GET['pid'], 'uid' => $_SESSION['AUTH_ID'], "ver_time" => "1900-01-01 10:00:00", "sub_lang" => $submission_lang]);
    $submission_id = $pdo->lastInsertId();
    
    $submission_type = "normal";
    if(isset($_GET['mode']))
    {
        if($_GET['mode']=="silent" and $_SESSION['AUTH_LEVEL'] <=5)
        {
            $submission_type = "silent";
        } else if($_GET['mode']=="recheck" and $_SESSION['AUTH_LEVEL'] <=3)
        {
            $submission_type = "recheck";
        }
    }

    savesubmission($submission_id, $_POST['sendtext'], $submission_lang);

    $tests = array();
    $db_query = $pdo->prepare('SELECT * FROM ALG_TEST_LIST WHERE problem_id=:pid');
    $db_query->execute(['pid' => $_GET['pid']]);
    while($row = $db_query->fetch())
	{
        array_push($tests, $row);
    }

    if(count($tests)<1)
    {
        kick();
    }


    $problem_id = $_GET['pid'];

    $myObj = new stdClass();

    $myObj->submission_id = $submission_id; 
    $myObj->submission_type = $submission_type;
	$myObj->user_id = $_SESSION['AUTH_ID'];
    $myObj->problem_id = $problem_id; 
    $myObj->submission_lang = $submission_lang;
    $myObj->tests = $tests;
    $myObj->listenerUrl = $site_domains[0];
    $myObj->submission_time = date('Y-m-d H:i:s');
    $myObj->submission_file = base64_encode(file_get_contents(zip_submission($submission_id)));

    $data = json_encode($myObj);

    mqsend($rabbit_mq_host, $rabbit_mq_port, $rabbit_mq_user, $rabbit_mq_password, $data); #credentials from config/config_init.php

    redirect("index.php?p=algresult&sid=".$submission_id);
    die;
?>