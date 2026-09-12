<?php
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if(!is_logged_in() || !$id) kick();

    try {
        $db_query = $pdo->prepare('SELECT PROBLEM_ID, maxpoints, problemset, comment FROM PROBLEMS WHERE PROBLEM_ID=:pid');
        $db_query->execute(['pid' => $id]);
        $row = $db_query->fetch();

        if(!$row) kick();
        if(trim((string)$row['comment']) === '') kick();
        $correct_anwsers = preg_split("/\r\n|\n|\r/", (string)$row['comment']);

        $count = 0;
        $a_ok = 0;

        foreach($correct_anwsers as $q)
        {
            if(!trim($q)) continue;
            $count++;
            $pts = 1;

            foreach(str_split($q) as $x)
            {
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

        $s_score = ($count*$row['maxpoints']!=0) ? $a_ok/$count*$row['maxpoints'] : 0;
        $s_score_percentage = ($row['maxpoints']>0) ? $s_score / $row['maxpoints'] * 100 : 0;

        $db_query = $pdo->prepare('INSERT INTO SUBMISSIONS (problem_id, problemset_id, user_id, verification_time, score, score_percentage, mode) VALUES(:pid, :sid, :uid, :vertime, :score, :percentage, 1)');
        $db_query->execute(['pid' => $row['PROBLEM_ID'], 'sid' => $row['problemset'], 'uid' => $_SESSION['AUTH_ID'], 'vertime' => date("Y-m-d H:i:s", time()), 'score' => $s_score, 'percentage' => $s_score_percentage]);
        $submission_id = $pdo->lastInsertId();

        redirect("index.php?p=testresult&sid=".$submission_id);
    } catch (Throwable $t) {
        extended_exception_handler($t);
        redirect("index.php?error");
    }
?>