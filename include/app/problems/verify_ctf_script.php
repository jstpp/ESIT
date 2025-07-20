<?php
    if(is_logged_in() and isset($_POST['ctf_flag']) and isset($_GET['id']))
    {
        $db_query = $pdo->prepare('SELECT * FROM PROBLEMS WHERE PROBLEM_ID=:pid');
		$db_query->execute(['pid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);

        $row = $db_query->fetch();

        if(preg_replace('/\s+/', '', $_POST['ctf_flag'])==preg_replace('/\s+/', '', $row['comment']))
        {
            $s_score_percentage = 100;
            $s_score = $row['maxpoints'];
        } else {
            $s_score_percentage = 0;
            $s_score = 0;
        }

        $db_query = $pdo->prepare('INSERT INTO SUBMISSIONS (problem_id, problemset_id, user_id, verification_time, score, score_percentage, mode) VALUES(:pid, :sid, :uid, :vertime, :score, :percentage, 1)');
		$db_query->execute(['pid' => filter_var($row['PROBLEM_ID'], FILTER_VALIDATE_INT), 'sid' => filter_var($row['problemset'], FILTER_VALIDATE_INT), 'uid' => $_SESSION['AUTH_ID'], 'vertime' => date("Y-m-d H:i:s", time()), 'score' => $s_score, 'percentage' => $s_score_percentage]);
        $submission_id = $pdo->lastInsertId();
        
        echo("<meta http-equiv='refresh' content='0; url=index.php?p=ctfresult&sid=".$submission_id."' />"); 
        die;
    } else {
        kick();
    }
?>