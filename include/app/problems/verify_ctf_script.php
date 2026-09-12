<?php
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if(!is_logged_in() || !isset($_POST['ctf_flag']) || !$id) kick();

    try {
        $db_query = $pdo->prepare('SELECT PROBLEM_ID, problemset, maxpoints, comment FROM PROBLEMS WHERE PROBLEM_ID=:pid');
        $db_query->execute(['pid' => $id]);
        $row = $db_query->fetch();

        if(!$row) kick();

        $user_flag = preg_replace('/\s+/', '', $_POST['ctf_flag']);
        $correct_flag = preg_replace('/\s+/', '', $row['comment']);
        if(hash_equals((string)$correct_flag, (string)$user_flag))
        {
            $s_score_percentage = 100;
            $s_score = $row['maxpoints'];
        } else {
            $s_score_percentage = 0;
            $s_score = 0;
        }

        $db_query = $pdo->prepare('INSERT INTO SUBMISSIONS (problem_id, problemset_id, user_id, verification_time, score, score_percentage, mode) VALUES(:pid, :sid, :uid, :vertime, :score, :percentage, 1)');
        $db_query->execute(['pid' => $row['PROBLEM_ID'], 'sid' => $row['problemset'], 'uid' => $_SESSION['AUTH_ID'], 'vertime' => date("Y-m-d H:i:s", time()), 'score' => $s_score, 'percentage' => $s_score_percentage]);
        $submission_id = $pdo->lastInsertId();
        
        redirect("index.php?p=ctfresult&sid=".$submission_id);
    } catch (Throwable $t) {
        extended_exception_handler($t);
        redirect("index.php?error");
    }
?>