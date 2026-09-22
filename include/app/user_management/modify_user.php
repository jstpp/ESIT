<?php
    if (!is_logged_in() || !has_permission('main.user_management.modify_user')) kick();
    if(!isset($_POST['username'], $_POST['name'], $_POST['surname'], $_POST['org'], $_POST['mail'], $_POST['role'], $_POST['password'])) kick();

    if(mb_strlen($_POST['username']) < 4 || mb_strlen($_POST['username']) > 30) redirect("index.php?p=admin&error#users");
    if(!preg_match('/^[\p{L}\p{N}_-]+$/u', $_POST['username'])) redirect("index.php?p=admin&error#users");
    if(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) redirect("index.php?p=admin&error#users");
    if(empty(trim($_POST['name'])) || empty(trim($_POST['surname']))) redirect("index.php?p=admin&error#users");

    try {
        $db_query = $pdo->prepare('SELECT priority FROM ROLES WHERE ROLE_ID=:rid');
        $db_query->execute(['rid'=>$_POST['role']]);
        $role_priority = ($role_priority_fetch = $db_query->fetch()) ? $role_priority_fetch['priority'] : null;
        if (!$role_priority || $role_priority<$_SESSION['AUTH_ROLE']['priority'] || $role_priority<1) redirect("index.php?p=admin&error#users");

        $user_id = filter_var($_GET['uid'], FILTER_VALIDATE_INT) ?? null;
        if(!$user_id) kick();

        $pdo->beginTransaction();
        $db_query = $pdo->prepare('UPDATE USERS SET username=:username, mail=:mail, name=:name, surname=:surname, organization=:org WHERE USER_ID=:uid');
        $db_query->execute([
            'username' => $_POST['username'], 
            'mail' => filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL), 
            'name' => trim($_POST['name']), 
            'surname' => trim($_POST['surname']), 
            'org' => $_POST['org'], 
            'uid' => $user_id
        ]);
        $db_query = $pdo->prepare('DELETE FROM AFFILIATION WHERE user_id=:uid');
        $db_query->execute(['uid' => $user_id]);
        $db_query = $pdo->prepare('INSERT INTO AFFILIATION (user_id, role_id) VALUES (:uid, :rid)');
        $db_query->execute(['uid' => $user_id, 'rid' => $_POST['role']]);
        $pdo->commit();
    } catch (Throwable $t) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        
        extended_exception_handler($t);
        redirect("index.php?p=admin&error#users");
    }

    redirect("index.php?p=admin#users");
?>