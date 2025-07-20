<?php
    function process_alg_create_root($pid)
	{
        mkdir("content/quests/".$pid, 0777);
        mkdir("content/quests/".$pid."/pdf", 0777, true);
		mkdir(__DIR__.'/../../../include/worker/alg/'.$pid, 0777, true);
        mkdir(__DIR__.'/../../../include/worker/alg/'.$pid."/in", 0777, true);
        mkdir(__DIR__.'/../../../include/worker/alg/'.$pid."/out", 0777, true);
	}

    function process_ctf_public_create_root($pid)
	{
		mkdir("content/ctf_public/".$pid, 0777);
	}

    function process_form_create_root($pid)
	{
		mkdir("content/quests/".$pid, 0777);
        mkdir("content/quests/".$pid."/pdf", 0777);
	}

    function process_alg_check($file, $mode)
	{
		$FileType = strtolower(pathinfo(basename($file["name"]),PATHINFO_EXTENSION));
		$check = filesize($file["tmp_name"]);

        if($check !== false) {
          	echo "File - OK";
          	$uploadOk = 1;
        } else {
          	echo "Not a file";
          	$uploadOk = 0;
        }

		if (file_exists(basename($file["name"]))) {
          	echo "Sorry, file already exists.";
          	$uploadOk = 0;
        }

		if ($file["size"] > 20000000) {
        	echo "Plik zajmuje zbyt wiele miejsca!";
        	$uploadOk = 0;
        }

		if($FileType != $mode ) {
          echo "Tylko pliki .".htmlentities($mode)." są dozwolone.";
          $uploadOk = 0;
        }

		if ($uploadOk == 0) {
          	return False;
        } else {
          	return True;
        }
	}

    function process_alg_file($file, $mode, $pid, $tid)
	{
		$index = "new_file";
		$target_file = __DIR__.'/../../../include/worker/alg/'.$pid."/".$mode."/".basename($file["name"]);
		$FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		$target_file = __DIR__.'/../../../include/worker/alg/'.$pid."/".$mode."/".$tid.".".$FileType;
		$check = filesize($file["tmp_name"]);

        $uploadOk = 1;
		if (file_exists($target_file)) {
          	echo "Sorry, file already exists.";
          	$uploadOk = 0;
        }

		if ($uploadOk == 0) {
          	echo "<br/>Przepraszamy, wystąpił błąd przy wysyłaniu. Zgłoś to administracji.";
        } else {
          	if (move_uploaded_file($file["tmp_name"], $target_file)) {
            	echo("<= OK =>");
          	} else {
            	echo "<br/>Przepraszamy, wystąpił błąd przy wysyłaniu. Zgłoś to administracji.";
          	}
        }

		return $target_file;
	}

    function process_alg_content($file, $mode, $pid)
	{
		$index = "new_file";
		$target_file = "content/quests/".$pid."/".$mode."/".basename($file["name"]);
		$FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		$target_file = "content/quests/".$pid."/".$mode."/".$pid.".".$FileType;
		$check = filesize($file["tmp_name"]);

        $uploadOk = 1;
		if (file_exists($target_file)) {
          	echo "Sorry, file already exists.";
          	$uploadOk = 0;
        }

		if ($uploadOk == 0) {
          	echo "<br/>Przepraszamy, wystąpił błąd przy wysyłaniu. Zgłoś to administracji.";
        } else {
          	if (move_uploaded_file($file["tmp_name"], $target_file)) {
            	echo("<= OK =>");
          	} else {
            	echo "<br/>Przepraszamy, wystąpił błąd przy wysyłaniu. Zgłoś to administracji.";
          	}
        }

		return $target_file;
	}

    function process_ctf_public_file($file, $pid)
	{
		$index = "new_file";
		$target_file = 'content/ctf_public/'.$pid."/".basename($file["name"]);
		$FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		$target_file = 'content/ctf_public/'.$pid."/".$pid.".".$FileType;
		$check = filesize($file["tmp_name"]);

        $uploadOk = 1;
		if (file_exists($target_file)) {
          	echo "Sorry, file already exists.";
          	$uploadOk = 0;
        }

		if ($uploadOk == 0) {
          	echo "<br/>Przepraszamy, wystąpił błąd przy wysyłaniu. Zgłoś to administracji.";
        } else {
          	if (move_uploaded_file($file["tmp_name"], $target_file)) {
            	echo("<= OK =>");
          	} else {
            	echo "<br/>Przepraszamy, wystąpił błąd przy wysyłaniu. Zgłoś to administracji.";
          	}
        }

		return $target_file;
	}

    if(!is_logged_in() or !has_a_priority(3)) kick();

    $db_query = $pdo->prepare('SELECT * FROM PROBLEMSETS WHERE SET_ID=:setid AND author_id=:aid OR SET_ID=:setidx AND :perm<3');
	$db_query->execute(['setid' => filter_var($_GET['sid'], FILTER_VALIDATE_INT), 'aid' => $_SESSION['AUTH_ID'], 'setidx' => filter_var($_GET['sid'], FILTER_VALIDATE_INT), 'perm' => $_SESSION['AUTH_LEVEL']]);
	$isfound = 0;

	if($row = $db_query->fetch())
	{
		$isfound++;
	}

    if($isfound!=1)
    {
        echo("<meta http-equiv='refresh' content='0; url=".$error_link."' />"); 
		die;
    }

    if(isset($_POST['problem_type']) and $_POST['problem_type']>0 and $_POST['problem_type']<=5)
    {
        try
        {
            $db_query = $pdo->prepare('INSERT INTO PROBLEMS (title, author_id, type, maxattempts, maxpoints, problemset, publish_time, isarchived) VALUES (:title, :aid, :type, :maxattempts, :maxpoints, :sid, :publishtime, :isarchived)');
            $db_query->execute(['title' => htmlentities($_POST['problem_title']), 'aid' => $_SESSION['AUTH_ID'], 'type' => filter_var($_POST['problem_type'], FILTER_VALIDATE_INT), 'maxattempts' => filter_var($_POST['problem_maxattempts'], FILTER_VALIDATE_INT), 'maxpoints' => filter_var($_POST['problem_points'], FILTER_VALIDATE_INT), 'sid' => filter_var($_GET['sid'], FILTER_VALIDATE_INT), 'publishtime' => $_POST['publish_time'], 'isarchived' => filter_var($_POST['problem_isarchived'], FILTER_VALIDATE_INT)]);
            $problem_id = $pdo->lastInsertId();
        } catch (Exception $e){
            redirect("index.php?p=sets&error");
        }
    }

    if((int)$_POST['problem_type']==1)
    {
        $i = 0;
        $j = 0;
        process_alg_create_root($problem_id);
        while (isset($_FILES['in_'.($i+1)]) and isset($_FILES['out_'.($i+1)]) and isset($_POST['memory_'.($i+1)]) and isset($_POST['time_'.($i+1)]))
        {
            if(process_alg_check($_FILES['in_'.($i+1)], "in") and process_alg_check($_FILES['out_'.($i+1)], "out"))
            {
                $db_query = $pdo->prepare('INSERT INTO ALG_TEST_LIST (test_author_id, problem_id, max_time, max_memory) VALUES (:aid, :pid, :time, :memory)');
                $db_query->execute(['aid' => $_SESSION['AUTH_ID'], 'pid' => $problem_id, 'time' => $_POST['time_'.($i+1)], 'memory' => $_POST['memory_'.($i+1)]]);
                $test_id = $pdo->lastInsertId();
                process_alg_file($_FILES['in_'.($i+1)], "in", $problem_id, $test_id);
                process_alg_file($_FILES['out_'.($i+1)], "out", $problem_id, $test_id);

                echo("Test ".($i+1)." - OK");
                $j++;
            } else {
                echo("Test ".($i+1)." - ERROR");
            }
            $i++;
        }
        if($j>0 and process_alg_check($_FILES['alg_file'], "pdf"))
        {
            process_alg_content($_FILES['alg_file'], "pdf", $problem_id);
        }
    } else if((int)$_POST['problem_type']==2)
    {
        if(isset($_FILES['ctf_file']) and process_alg_check($_FILES['ctf_file'], strtolower(pathinfo($_FILES['ctf_file']['name'],PATHINFO_EXTENSION))) and isset($_POST['ctf_flag']))
        {
            process_ctf_public_create_root($problem_id);
            process_ctf_public_file($_FILES['ctf_file'], $problem_id);

            $db_query = $pdo->prepare('UPDATE PROBLEMS SET comment=:flag WHERE PROBLEM_ID=:pid');
            $db_query->execute(['flag' => $_POST['ctf_flag'], 'pid' => $problem_id]);
        } else {
            kick();
        }



    } else if((int)$_POST['problem_type']==3)
    {
        $i = 0;
        $j = 0;
        $correct = '';
        while(isset($_POST['s_question_'.($i+1)]))
        {
            $aws = $_POST['correct_'.($i+1)];

            $anwsers = array("a" => $_POST['q_a_'.($i+1)], "b" => $_POST['q_b_'.($i+1)], "c" => $_POST['q_c_'.($i+1)], "d" => $_POST['q_d_'.($i+1)]);
            $db_query = $pdo->prepare('INSERT INTO TEST_QUESTIONS (problem_id, question, anwsers) VALUES (:pid, :question, :anwsers)');
            $db_query->execute(['pid' => $problem_id, 'question'=>$_POST['s_question_'.($i+1)], 'anwsers' => strval(json_encode($anwsers))]);
            $i++;
            $correct = $correct.$aws.'
';

            $db_query = $pdo->prepare('UPDATE PROBLEMS SET comment=:correct WHERE PROBLEM_ID=:pid');
            $db_query->execute(['correct' => $correct, 'pid' => $problem_id]);
        }



    } else if((int)$_POST['problem_type']==4)
    {
        $i = 0;
        $j = 0;
        $correct = '';
        while(isset($_POST['m_question_'.($i+1)]))
        {
            $aws = '';
            if (isset($_POST['correct_a_'.($i+1)]))
            {
                $aws = $aws.'a';
            }
            if (isset($_POST['correct_b_'.($i+1)]))
            {
                $aws = $aws.'b';
            } 
            if (isset($_POST['correct_c_'.($i+1)]))
            {
                $aws = $aws.'c';
            }
            if (isset($_POST['correct_d_'.($i+1)]))
            {
                $aws = $aws.'d';
            }

            $anwsers = array("a" => $_POST['qm_a_'.($i+1)], "b" => $_POST['qm_b_'.($i+1)], "c" => $_POST['qm_c_'.($i+1)], "d" => $_POST['qm_d_'.($i+1)]);
            $db_query = $pdo->prepare('INSERT INTO TEST_QUESTIONS (problem_id, question, anwsers) VALUES (:pid, :question, :anwsers)');
            $db_query->execute(['pid' => $problem_id, 'question'=>$_POST['m_question_'.($i+1)], 'anwsers' => strval(json_encode($anwsers))]);
            $i++;
            $correct = $correct.$aws.'
';

            $db_query = $pdo->prepare('UPDATE PROBLEMS SET comment=:correct WHERE PROBLEM_ID=:pid');
            $db_query->execute(['correct' => $correct, 'pid' => $problem_id]);
        }



    } else if((int)$_POST['problem_type']==5)
    {
        process_form_create_root($problem_id);
        process_alg_check($_FILES['alg_file'], "pdf");
        process_alg_content($_FILES['alg_file'], "pdf", $problem_id);
    } else {
        kick();
    }

    redirect('index.php?p=quest&id='.filter_var($problem_id, FILTER_VALIDATE_INT));
?>