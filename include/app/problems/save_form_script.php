<?php
    if(is_logged_in() and isset($_POST['fareahidden']) and isset($_GET['id']))
    {
        $db_query = $pdo->prepare('SELECT * FROM PROBLEMS WHERE PROBLEM_ID=:pid');
		$db_query->execute(['pid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);

        $row = $db_query->fetch();


        $db_query = $pdo->prepare('INSERT INTO SUBMISSIONS (problem_id, problemset_id, user_id, verification_time, score, score_percentage, mode, content) VALUES(:pid, :sid, :uid, :vertime, :score, :percentage, 1, :content)');
		$db_query->execute(['pid' => filter_var($row['PROBLEM_ID'], FILTER_VALIDATE_INT), 'sid' => filter_var($row['problemset'], FILTER_VALIDATE_INT), 'uid' => $_SESSION['AUTH_ID'], 'vertime' => date("1900-01-01 10:00:00"), 'score' => -1, 'percentage' => -1, 'content' => $_POST['fareahidden']]);
        $submission_id = $pdo->lastInsertId();
        
        echo("<meta http-equiv='refresh' content='0; url=index.php?p=formresult&sid=".$submission_id."' />"); 
        die;
    } else {
        kick();
    }
?>