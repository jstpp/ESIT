<?php
    function unsuccessful_change($pdo)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $notification_content = "Niepomyślna próba zmiany hasła.";
            $db_query = $pdo->prepare('INSERT INTO NOTIFICATIONS (user_id, content, type) VALUES (:uid, :content, "warn")');
            $db_query->execute(['uid' => $_SESSION['AUTH_ID'], 'content' => $notification_content]);
        }
        redirect('index.php?p=settings&error');
    }

    if(!is_logged_in()) force_to_login();
    if(!isset($_POST['password_old'], $_POST['password_new1'], $_POST['password_new2']) || $_POST['password_new2']!=$_POST['password_new1'] || strlen($_POST['password_new1'])<2) unsuccessful_change($pdo);
    
    $db_query = $pdo->prepare('SELECT password FROM USERS WHERE USER_ID = :uid');
    $db_query->execute(['uid' => $_SESSION['AUTH_ID']]);
    $user = $db_query->fetch();

    if (!$user || !password_verify($_POST['password_old'], $user['password'])) unsuccessful_change($pdo);

    $db_query = $pdo->prepare('UPDATE USERS SET password=:password WHERE USER_ID=:uid');
    $db_query->execute(['password' => password_hash($_POST['password_new1'], PASSWORD_ARGON2ID), 'uid' => $_SESSION['AUTH_ID']]);

    $notification_content = "Twoje hasło zostało zmienione.";
    $db_query = $pdo->prepare('INSERT INTO NOTIFICATIONS (user_id, content, type) VALUES (:uid, :content, "success")');
    $db_query->execute(['uid' => $_SESSION['AUTH_ID'], 'content' => $notification_content]);

    redirect('index.php?p=settings');
?>