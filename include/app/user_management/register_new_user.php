<?php
    if(!isset($_POST['username'], $_POST['name'], $_POST['surname'], $_POST['org'], $_POST['mail'], $_POST['pass'], $_POST['pass_repeat'])) kick();
    if(is_logged_in()) kick();

    try {
        if(!is_string($_POST['username']) 
        || !is_string($_POST['pass'])
        || !is_string($_POST['org']) 
        || !is_string($_POST['mail']) 
        || !is_string($_POST['name'])
        || !is_string($_POST['surname'])) kick();
        if(!preg_match('/^[\p{L}\p{N}_-]+$/u', $_POST['username'])) redirect("../rejestracja.php?error");
        if($_POST['pass']!=$_POST['pass_repeat']) redirect("../rejestracja.php?error");
        if(mb_strlen($_POST['pass'])<8 || mb_strlen($_POST['pass'])>50 || mb_strlen($_POST['username'])<6) redirect("../rejestracja.php?error");
        if(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) redirect("../rejestracja.php?error");
        if(is_an_user($_POST['username']) || is_an_user($_POST['mail'])) redirect("../rejestracja.php?error");
        if(empty(trim($_POST['name'])) || empty(trim($_POST['surname'])) || empty(trim($_POST['org']))) redirect("../rejestracja.php?error");
    } catch (Throwable $t) {
        extended_exception_handler($t);
        kick();
    }

    try {
        $pdo->beginTransaction();

        $db_query = $pdo->prepare('SELECT COUNT(*) AS count FROM USERS');
        $db_query->execute();
        $priority = ($db_query->fetch()['count']==0) ? 1 : 10;

        $db_query = $pdo->prepare('INSERT INTO USERS (username, password, role, mail, name, surname, organization) VALUES (:username, :password, :priority, :mail, :name, :surname, :org)');
        $db_query->execute([
            'username' => $_POST['username'], 
            'password' => password_hash($_POST['pass'], PASSWORD_ARGON2ID),
            'priority'=> $priority, 
            'mail' => filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL), 
            'name' => trim($_POST['name']), 
            'surname' => trim($_POST['surname']), 
            'org' => trim($_POST['org'])
        ]);

        $pdo->commit();

        redirect("../login/?response=registered");
    } catch (Throwable $t) {
        try {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
        } catch (Throwable $t2)
        {
            extended_exception_handler($t2);
        }
        extended_exception_handler($t);
        redirect("../rejestracja.php?error");
    }
?>