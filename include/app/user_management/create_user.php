<?php
    if(!isset($_POST['username'], $_POST['name'], $_POST['surname'], $_POST['org'], $_POST['mail'], $_POST['priority'], $_POST['password'])) kick();
    if(!has_a_priority(filter_var($_POST['priority'], FILTER_VALIDATE_INT)-1)) kick();

    if(mb_strlen($_POST['username']) < 4 || mb_strlen($_POST['username']) > 30) redirect("index.php?p=admin&error#users");
    if(!preg_match('/^[\p{L}\p{N}_-]+$/u', $_POST['username'])) redirect("index.php?p=admin&error#users");
    if(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) redirect("index.php?p=admin&error#users");
    if(is_an_user($_POST['username']) || is_an_user($_POST['mail'])) redirect("index.php?p=admin&error#users");
    if(empty(trim($_POST['name'])) || empty(trim($_POST['surname'])) || empty(trim($_POST['org']))) redirect("index.php?p=admin&error#users");

    try {
        $db_query = $pdo->prepare('INSERT INTO USERS (username, password, role, mail, name, surname, organization) VALUES (:username, :password, :priority, :mail, :name, :surname, :org)');
        $db_query->execute([
            'username' => $_POST['username'], 
            'password' => password_hash($_POST['password'], PASSWORD_ARGON2ID),
            'priority'=> filter_var($_POST['priority'], FILTER_VALIDATE_INT), 
            'mail' => filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL), 
            'name' => trim($_POST['name']), 
            'surname' => trim($_POST['surname']), 
            'org' => trim($_POST['org'])
        ]);
    } catch (Throwable $t) {
        extended_exception_handler($t);
        redirect("index.php?p=admin&error#users");
    }
    redirect("index.php?p=admin#users");
?>