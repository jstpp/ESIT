<?php
    if(!is_logged_in() || !has_permission('main.user_management.remove_user')) kick();

    $uid = filter_var($_GET['uid'], FILTER_VALIDATE_INT);
    if($uid===False || $uid===null) kick();
    if(get_roles($uid)[0]['priority']<$_SESSION['AUTH_ROLE']['priority']) kick();

    try {
        $pdo->beginTransaction();
        $db_query = $pdo->prepare('DELETE FROM USERS WHERE USER_ID=:uid');
        $db_query->execute(['uid' => $uid]);

        $db_query = $pdo->prepare('DELETE FROM AFFILIATION WHERE user_id=:uid');
        $db_query->execute(['uid' => $uid]);
        $pdo->commit();

        redirect("index.php?p=admin#users");
    } catch (Throwable $t) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        extended_exception_handler($t);
        redirect("index.php?p=admin&error#users");
    }
?>