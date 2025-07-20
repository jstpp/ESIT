<?php
    if(is_logged_in())
    {
        if(isset($_POST['password_old']) and isset($_POST['password_new1']) and isset($_POST['password_new2']) and $_POST['password_new2']==$_POST['password_new1'])
        {
            $db_query = $pdo->prepare('UPDATE USERS SET password=:password WHERE USER_ID=:uid');
            $db_query->execute(['password' => hash('sha3-384', $_POST['password_new1']), 'uid' => $_SESSION['AUTH_ID']]);
        }

        $notification_content = "Twoje hasło zostało zmienione.";
        $db_query = $pdo->prepare('INSERT INTO NOTIFICATIONS (user_id, content, type) VALUES (:uid, :content, "success")');
        $db_query->execute(['uid' => $_SESSION['AUTH_ID'], 'content' => $notification_content]);

        header('Location: index.php?p=settings');
        die;
    } else {
        force_to_login();
    }
?>