<?php
    require_once('modules/composer/vendor/autoload.php');

    $purifier_config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($purifier_config);

    function ensure_directory(string $path): void
    {
        if (!is_dir($path) && !mkdir($path, 0755, true) && !is_dir($path)) {
            throw new Exception("Cannot ensure usability of ".$path);
        }

        if(!(is_dir($path) && is_writable($path))) throw new Exception("Cannot ensure usability of ".$path);
    }

    function process_alg_create_root(int $pid): bool
	{
        try {
            ensure_directory(__DIR__.'/../../../include/worker/alg/'.(int)$pid);
            ensure_directory(__DIR__.'/../../../include/worker/alg/'.(int)$pid."/in");
            ensure_directory(__DIR__.'/../../../include/worker/alg/'.(int)$pid."/out");
            ensure_directory(__DIR__.'/../../../include/worker/alg/'.(int)$pid."/pdf");
        } catch (Throwable $t) {
            extended_exception_handler($t);
            return False;
        }
        return True;
	}

    function process_ctf_public_create_root(int $pid): bool
	{
        try {
            ensure_directory(__DIR__.'/../../../include/worker/ctf/'.(int)$pid);
        } catch (Throwable $t) {
            extended_exception_handler($t);
            return False;
        }
        return True;
	}

    function process_form_create_root(int $pid): bool
	{
        try {
            ensure_directory(__DIR__.'/../../../include/worker/alg/'.(int)$pid); 
            ensure_directory(__DIR__.'/../../../include/worker/alg/'.(int)$pid."/pdf"); 
        } catch (Throwable $t) {
            extended_exception_handler($t);
            return False;
        }
        return True;
	}

    function process_alg_check(
        array $file, 
        string $required_extension,
        int $max_size = 20000000
    ): bool
	{
        try {
            if (!isset($file['error'], $file['tmp_name'], $file['name']) || $file['error'] !== UPLOAD_ERR_OK) return False;
            if (!is_uploaded_file($file['tmp_name'])) return False;

            $restricted_extensions = ['php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phar', 'shtml', 'phtml', 'pht', 'pl', 'cgi', 'htaccess'];
            $file_extension = strtolower(pathinfo(basename($file["name"]),PATHINFO_EXTENSION));
            if (in_array($file_extension, $restricted_extensions, true)) return False;

            $actual_size = filesize($file['tmp_name']);
            if ($actual_size === false || $actual_size <= 0 || $actual_size > $max_size) return False;
            if (!isset($file['size']) || (int)$file['size'] !== $actual_size) return False;
            if ($file["size"] > $max_size) return False;

            if ($file_extension != $required_extension) return False;

            return True;
        } catch (Throwable $t) {
            extended_exception_handler($t);
            return False;
        }
	}

    function process_alg_file(
        array $file, 
        string $mode, 
        int $pid, 
        int $tid
    ): ?string
	{
        $pid = (int)$pid;
        $tid = (int)$tid;
        try {
            $target_file = __DIR__.'/../../../include/worker/alg/'.$pid."/".$mode."/".basename($file["name"]);
            $file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            $target_file = __DIR__.'/../../../include/worker/alg/'.$pid."/".$mode."/".$tid.".".$file_type;

            if (file_exists($target_file)) return null;
            if (!move_uploaded_file($file["tmp_name"], $target_file)) return null;

            return $target_file;
        } catch (Throwable $t) {
            extended_exception_handler($t);
            return null;
        }
	}

    function process_alg_content(
        array $file, 
        string $mode, 
        int $pid
    ): ?string
	{
        $pid = (int)$pid;
        try {
            $target_file = __DIR__.'/../../../include/worker/alg/'.$pid."/".$mode."/".basename($file["name"]);
            $file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            $target_file = __DIR__.'/../../../include/worker/alg/'.$pid."/".$mode."/".$pid.".".$file_type;

            if (file_exists($target_file)) return null;
            if (!move_uploaded_file($file["tmp_name"], $target_file)) return null;

            return $target_file;
        } catch (Throwable $t) {
            extended_exception_handler($t);
            return null;
        }
	}

    function process_ctf_public_file(
        array $file, 
        int $pid
    ): ?string
	{
        $pid = (int)$pid;
        try {
            $target_file = __DIR__.'/../../../include/worker/ctf/'.$pid."/".basename($file["name"]);
            $file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            $target_file = __DIR__.'/../../../include/worker/ctf/'.$pid."/".$pid.".".$file_type;

            if (file_exists($target_file)) return null;
            if (!move_uploaded_file($file["tmp_name"], $target_file)) return null;

            return $target_file;
        } catch (Throwable $t) {
            extended_exception_handler($t);
            return null;
        }
	}

    $sid = filter_var($_GET['sid'] ?? null, FILTER_VALIDATE_INT);
    if(!has_permission('main.channels.add_problem') || !$sid || $sid<=0) kick();

    $db_query = $pdo->prepare('SELECT * FROM PROBLEMSETS WHERE SET_ID=:setid AND author_id=:aid');
	$db_query->execute(['setid' => $sid, 'aid' => $_SESSION['AUTH_ID']]);
    $row = $db_query->fetch();

	if(!$row) kick();

    if(isset($_POST['problem_type']) and $_POST['problem_type']>0 and $_POST['problem_type']<=5)
    {
        try
        {
            $db_query = $pdo->prepare('INSERT INTO CONTENT (title, author_id, type, maxattempts, maxpoints, problemset, result_publish_time, publish_time, isarchived) VALUES (:title, :aid, :type, :maxattempts, :maxpoints, :sid, :resultpublishtime, :publishtime, :isarchived)');
            $db_query->execute(['title' => $purifier->purify($_POST['problem_title']), 'aid' => $_SESSION['AUTH_ID'], 'type' => filter_var($_POST['problem_type'], FILTER_VALIDATE_INT), 'maxattempts' => filter_var($_POST['problem_maxattempts'], FILTER_VALIDATE_INT), 'maxpoints' => filter_var($_POST['problem_points'], FILTER_VALIDATE_INT), 'sid' => filter_var($_GET['sid'], FILTER_VALIDATE_INT), 'resultpublishtime' => $_POST['result_publish_time'], 'publishtime' => $_POST['publish_time'], 'isarchived' => filter_var($_POST['problem_isarchived'], FILTER_VALIDATE_INT)]);
            $problem_id = $pdo->lastInsertId();
        } catch (Throwable $t){
            extended_exception_handler($t);
            redirect("index.php?p=search&error");
        }
    }
    
    if(!isset($problem_id)) redirect("index.php?p=search&error");

    try {
        if((int)$_POST['problem_type']==1)
        {
            $i = 0;
            $j = 0;
            if (!process_alg_create_root($problem_id)) {
                throw new Exception("Cannot create algorithm directories");
            }
            while (isset($_FILES['in_'.($i+1)]) and isset($_FILES['out_'.($i+1)]) and isset($_POST['memory_'.($i+1)]) and isset($_POST['time_'.($i+1)]))
            {
                if(process_alg_check($_FILES['in_'.($i+1)], "in") and process_alg_check($_FILES['out_'.($i+1)], "out"))
                {
                    $db_query = $pdo->prepare('INSERT INTO ALG_TEST_LIST (test_author_id, problem_id, max_time, max_memory, weight) VALUES (:aid, :pid, :time, :memory, :weight)');
                    $db_query->execute(['aid' => $_SESSION['AUTH_ID'], 'pid' => $problem_id, 'time' => $_POST['time_'.($i+1)], 'memory' => $_POST['memory_'.($i+1)], 'weight' => $_POST['weight_'.($i+1)]]);
                    $test_id = $pdo->lastInsertId();

                    $input_path = process_alg_file($_FILES['in_'.($i+1)], "in", $problem_id, $test_id);
                    $output_path = process_alg_file($_FILES['out_'.($i+1)], "out", $problem_id, $test_id);
                    if ($input_path === null || $output_path === null) {
                        throw new Exception("Cannot save algorithm test files");
                    }

                    echo("Test ".($i+1)." - OK");
                    $j++;
                }
                $i++;
            }
            if($j>0 and process_alg_check($_FILES['alg_file'], "pdf")) process_alg_content($_FILES['alg_file'], "pdf", $problem_id);
        } else if((int)$_POST['problem_type']==2)
        {
            if(!isset($_FILES['ctf_file'], $_POST['ctf_flag']) || !process_alg_check($_FILES['ctf_file'], strtolower(pathinfo($_FILES['ctf_file']['name'],PATHINFO_EXTENSION)))) kick();
            
            if (!process_ctf_public_create_root($problem_id)) {
                throw new Exception("Cannot create ctf directories");
            }
            if(!process_ctf_public_file($_FILES['ctf_file'], $problem_id)) {
                throw new Exception("Cannot save ctf files");
            }

            $db_query = $pdo->prepare('UPDATE CONTENT SET comment=:flag WHERE CONTENT_ID=:pid');
            $db_query->execute(['flag' => $_POST['ctf_flag'], 'pid' => $problem_id]);
            
        } else if((int)$_POST['problem_type']==3)
        {
            $i = 0;
            $j = 0;
            $correct = '';
            while(isset($_POST['s_question_'.($i+1)]))
            {
                $aws = $_POST['correct_'.($i+1)];

                $answers = array("a" => $_POST['q_a_'.($i+1)], "b" => $_POST['q_b_'.($i+1)], "c" => $_POST['q_c_'.($i+1)], "d" => $_POST['q_d_'.($i+1)]);
                $db_query = $pdo->prepare('INSERT INTO TEST_QUESTIONS (problem_id, question, answers) VALUES (:pid, :question, :answers)');
                $db_query->execute(['pid' => $problem_id, 'question'=>$_POST['s_question_'.($i+1)], 'answers' => strval(json_encode($answers))]);
                $i++;
                $correct = $correct.$aws.'
    ';
            }
            $db_query = $pdo->prepare('UPDATE CONTENT SET comment=:correct WHERE CONTENT_ID=:pid');
            $db_query->execute(['correct' => $correct, 'pid' => $problem_id]);


        } else if((int)$_POST['problem_type']==4)
        {
            $i = 0;
            $j = 0;
            $correct = '';
            while(isset($_POST['m_question_'.($i+1)]))
            {
                $aws = '';
                if (isset($_POST['correct_a_'.($i+1)])) $aws = $aws.'a';
                if (isset($_POST['correct_b_'.($i+1)])) $aws = $aws.'b';
                if (isset($_POST['correct_c_'.($i+1)])) $aws = $aws.'c';
                if (isset($_POST['correct_d_'.($i+1)])) $aws = $aws.'d';

                $answers = array("a" => $_POST['qm_a_'.($i+1)], "b" => $_POST['qm_b_'.($i+1)], "c" => $_POST['qm_c_'.($i+1)], "d" => $_POST['qm_d_'.($i+1)]);
                $db_query = $pdo->prepare('INSERT INTO TEST_QUESTIONS (problem_id, question, answers) VALUES (:pid, :question, :answers)');
                $db_query->execute(['pid' => $problem_id, 'question'=>$purifier->purify($_POST['m_question_'.($i+1)]), 'answers' => $purifier->purify(strval(json_encode($answers)))]);
                $i++;
                $correct = $correct.$aws.'
    ';
            }
            $db_query = $pdo->prepare('UPDATE CONTENT SET comment=:correct WHERE CONTENT_ID=:pid');
            $db_query->execute(['correct' => $correct, 'pid' => $problem_id]);


        } else if((int)$_POST['problem_type']==5)
        {
            if(!process_alg_check($_FILES['alg_file'], "pdf")) throw new Exception("Cannot save PDF file");
            if (!process_form_create_root($problem_id)) {
                throw new Exception("Cannot create form directories");
            }
            if (!process_alg_content($_FILES['alg_file'], "pdf", $problem_id)) {
                throw new Exception("Cannot create form PDF file");
            }
        } else {
            kick();
        }
    } catch (Throwable $t){
        extended_exception_handler($t);
        redirect("index.php?p=search&error");
    }

    try {
        $db_query = $pdo->prepare('INSERT INTO NOTIFICATIONS (user_id, content) SELECT f.user_id, :content FROM FOLLOWS f WHERE f.channel_id = :cid');
        $db_query->execute(['content' => 'New problems in channels followed by You.', 'cid' => $row['channel_id']]);

    } catch (Throwable $t){
        extended_exception_handler($t);
        redirect("index.php?p=search&error");
    }

    redirect('index.php?p=content&id='.filter_var($problem_id, FILTER_VALIDATE_INT));
?>