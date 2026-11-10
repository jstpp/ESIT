<?php
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if(!is_logged_in() || !$id) kick();

    try {
        $db_query = $pdo->prepare('SELECT CONTENT_ID, maxpoints, problemset, comment FROM CONTENT WHERE CONTENT_ID=:pid');
        $db_query->execute(['pid' => $id]);
        $row = $db_query->fetch();

        if(!$row) kick();
        if(trim((string)$row['comment']) === '') kick();
        $correct_answers = preg_split("/\r\n|\n|\r/", (string)$row['comment']);

        $count = 0;
        $a_ok = 0;

        foreach($correct_answers as $q)
        {
            if(!trim($q)) continue;

            $count++;
            $pts = 1;

            $correct = str_split(trim($q));
            $submitted = [];

            foreach(['a', 'b', 'c', 'd'] as $x)
            {
                if(isset($_POST[$count."_".$x]))
                {
                    $submitted[] = $x;
                }
            }

            sort($correct);
            sort($submitted);

            if($correct !== $submitted)
            {
                $pts = 0;
            }

            $a_ok += $pts;
        }

        $s_score = ($count*$row['maxpoints']!=0) ? $a_ok/$count*$row['maxpoints'] : 0;
        $s_score_percentage = ($row['maxpoints']>0) ? $s_score / $row['maxpoints'] * 100 : 0;

        $db_query = $pdo->prepare('INSERT INTO SUBMISSIONS (problem_id, problemset_id, user_id, verification_time, score, score_percentage, mode) VALUES(:pid, :sid, :uid, :vertime, :score, :percentage, 1)');
        $db_query->execute(['pid' => $row['CONTENT_ID'], 'sid' => $row['problemset'], 'uid' => $_SESSION['AUTH_ID'], 'vertime' => date("Y-m-d H:i:s", time()), 'score' => $s_score, 'percentage' => $s_score_percentage]);
        $submission_id = $pdo->lastInsertId();

        redirect("index.php?p=testresult&sid=".$submission_id);
    } catch (Throwable $t) {
        extended_exception_handler($t);
        redirect("index.php?error");
    }
?>