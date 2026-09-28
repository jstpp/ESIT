<?php
    $sid = filter_var($_GET['sid'] ?? null, FILTER_VALIDATE_INT);
    if(!is_logged_in() || !has_permission('main.solutions.request_recheck') || !$sid) kick();

    $db_query = $pdo->prepare('INSERT INTO REEVALUATION_REQUESTS (user_id, submission_id) VALUES (:uid, :sid)');
    $db_query->execute(['uid' => $_SESSION['AUTH_ID'], 'sid' => $sid]);

    redirect($_SERVER['HTTP_REFERER']);
?>