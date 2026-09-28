<?php
    if(!is_logged_in()) force_to_login();
    if($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['ace_theme'])) redirect('index.php?p=settings&error');

    $ace_theme = htmlspecialchars($_POST['ace_theme'], ENT_QUOTES, 'UTF-8');
    $dark_mode = isset($_POST['app_dark_theme']) && $_POST['app_dark_theme']==1 ? 1 : 0;

    $settings = array(
        'code_editor_theme' => $ace_theme, 
        'dark_mode' => $dark_mode
    );

    $db_query = $pdo->prepare('UPDATE USERS SET settings=:settings WHERE USER_ID=:uid');
    $db_query->execute(['settings' => json_encode($settings), 'uid' => $_SESSION['AUTH_ID']]);

    $notification_content = "Pomyślnie zmieniono ustawienia wyglądu.";
    $db_query = $pdo->prepare('INSERT INTO NOTIFICATIONS (user_id, content, type) VALUES (:uid, :content, "success")');
    $db_query->execute(['uid' => $_SESSION['AUTH_ID'], 'content' => $notification_content]);

    redirect('index.php?p=settings');
?>