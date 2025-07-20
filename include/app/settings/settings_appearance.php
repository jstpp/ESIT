<?php
    if(is_logged_in())
    {
        $settings = array('code_editor_theme' => $_POST['ace_theme'], 'dark_mode' => $_POST['app_dark_theme']);

        $db_query = $pdo->prepare('UPDATE USERS SET settings=:settings WHERE USER_ID=:uid');
        $db_query->execute(['settings' => json_encode($settings), 'uid' => $_SESSION['AUTH_ID']]);

        $notification_content = "Pomyślnie zmieniono ustawienia wyglądu.";
        $db_query = $pdo->prepare('INSERT INTO NOTIFICATIONS (user_id, content, type) VALUES (:uid, :content, "success")');
        $db_query->execute(['uid' => $_SESSION['AUTH_ID'], 'content' => $notification_content]);

        header('Location: index.php?p=settings');
        die;
    } else {
        force_to_login();
    }
?>