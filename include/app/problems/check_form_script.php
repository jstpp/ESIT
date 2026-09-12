<?php
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if(!is_logged_in() || !has_a_priority(4) || !isset($_POST['form_pts']) || !$id) kick();

    try {
        $db_query = $pdo->prepare('UPDATE SUBMISSIONS SET verification_time=:vertime, score=:score1, score_percentage=(100*:score2/(SELECT DISTINCT maxpoints FROM PROBLEMS INNER JOIN SUBMISSIONS ON PROBLEMS.PROBLEM_ID=SUBMISSIONS.problem_id WHERE SUBMISSION_ID=:sid1)), comment=:comment WHERE SUBMISSION_ID=:sid2');
        $db_query->execute(['vertime' => date("Y-m-d H:i:s", time()), 'score1'=>(int)$_POST['form_pts'], 'score2'=>(int)$_POST['form_pts'], 'sid1' => $id, 'sid2' => $id, 'comment' => $_POST['form_comment'] ?? '-']);

        redirect("index.php?p=formresult&sid=".filter_var($_GET['id'], FILTER_VALIDATE_INT));
    } catch (Throwable $t) {
        extended_exception_handler($t);
        redirect("index.php?error");
    }
?>