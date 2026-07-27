<?php
    if(!is_logged_in() || !has_a_priority(3)) kick();

    $uid = filter_var($_GET['uid'], FILTER_VALIDATE_INT);
    if($uid===False || $uid===null) kick();

    try {
        $db_query = $pdo->prepare('DELETE FROM USERS WHERE USER_ID=:uid AND role>:priority');
        $db_query->execute([
            'uid' => $uid,
            'priority' => $_SESSION['AUTH_LEVEL']
        ]);

        redirect("index.php?p=admin");
    } catch (Throwable $t) {
        extended_exception_handler($t);
        redirect("index.php?p=admin&error#users");
    }
?>