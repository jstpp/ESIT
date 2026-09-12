<?php
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if(!is_logged_in() || !isset($_POST['fareahidden']) || !$id) kick();

    try {
        $db_query = $pdo->prepare('SELECT PROBLEM_ID, problemset FROM PROBLEMS WHERE PROBLEM_ID=:pid');
        $db_query->execute(['pid' => $id]);
        $row = $db_query->fetch();

        if(!$row) kick();

        $db_query = $pdo->prepare('INSERT INTO SUBMISSIONS (problem_id, problemset_id, user_id, verification_time, score, score_percentage, mode, content) VALUES(:pid, :sid, :uid, :vertime, :score, :percentage, 1, :content)');
        $db_query->execute(['pid' => $row['PROBLEM_ID'], 'sid' => $row['problemset'], 'uid' => $_SESSION['AUTH_ID'], 'vertime' => "1900-01-01 10:00:00", 'score' => -1, 'percentage' => -1, 'content' => $_POST['fareahidden']]);
        $submission_id = $pdo->lastInsertId();
        
        redirect("index.php?p=formresult&sid=".$submission_id);
    } catch (Throwable $t) {
        extended_exception_handler($t);
        redirect("index.php?error");
    }
?>