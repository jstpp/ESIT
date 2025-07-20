<?php
    if(is_logged_in() and isset($_GET['id']))
    {
        $db_query = $pdo->prepare('SELECT * FROM PROBLEMS WHERE PROBLEM_ID=:pid');
		$db_query->execute(['pid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);

        $row = $db_query->fetch();
        $correct_anwsers = preg_split("/\r\n|\n|\r/", $row['comment']);

        $count = 0;
        $count_correct = 0;
        $a_wr = 0;

        foreach($correct_anwsers as $q)
        {
            if(strlen($q)>=1)
            {
                $count++;
                $pts = 1;
                foreach(str_split($q) as $x)
                {
                    $count_correct++;
                    if(isset($_POST[$count."_".$x]) and $_POST[$count."_".$x]==$x)
                    {
                        unset($_POST[$count."_".$x]);
                    } else if (isset($_POST[$count]) and $_POST[$count]==$x)
                    {
                        unset($_POST[$count]);
                    } else {
                        $pts = 0;
                    }
                }
                $a_ok += $pts;
            }
        }

        $s_score = $a_ok/$count*$row['maxpoints'];
        $s_score_percentage = $s_score / $row['maxpoints'] * 100;



        $db_query = $pdo->prepare('INSERT INTO SUBMISSIONS (problem_id, problemset_id, user_id, verification_time, score, score_percentage, mode) VALUES(:pid, :sid, :uid, :vertime, :score, :percentage, 1)');
		$db_query->execute(['pid' => filter_var($row['PROBLEM_ID'], FILTER_VALIDATE_INT), 'sid' => filter_var($row['problemset'], FILTER_VALIDATE_INT), 'uid' => $_SESSION['AUTH_ID'], 'vertime' => date("Y-m-d H:i:s", time()), 'score' => $s_score, 'percentage' => $s_score_percentage]);
        $submission_id = $pdo->lastInsertId();

        echo("<meta http-equiv='refresh' content='0; url=index.php?p=testresult&sid=".$submission_id."' />"); 
        die;
    } else {
        kick();
    }
?>