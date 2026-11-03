<?php
    if(!is_logged_in() || !has_permission('main.channels.create_channel')) kick();
    if(!isset($_POST['channel_setname'], $_POST['channel_description'], $_POST['channel_publish_time'])) redirect("index.php?p=search&error");

    $isarchived = (isset($_POST['channel_isactive']) && $_POST['channel_isactive'] === '1') ? 0 : 1;
    
    if(isset($_FILES["channel_set_img"]) && $_FILES["channel_set_img"]["error"] === UPLOAD_ERR_OK) {
        $image = load_img_input($_FILES["channel_set_img"], "../img/problemsets/header");
    } else {
        $image = load_img_input();
    }

    $title = trim($_POST['channel_setname']);
    if(empty($title)) redirect("index.php?p=search&error");
    
    try {
        $db_query = $pdo->prepare('INSERT INTO CHANNELS (title, author_id, description, publish_time, isarchived, img_path) VALUES (:title, :aid, :desc, :publishtime, :isarchived, :img_path)');
        $db_query->execute([
            'title' => $title, 
            'aid' => $_SESSION['AUTH_ID'],
            'desc'=> trim($_POST['channel_description']),
            'publishtime' => $_POST['channel_publish_time'],
            'img_path' => $image,
            'isarchived' => $isarchived
        ]);
    } catch (Throwable $t) {
        extended_exception_handler($t);
        redirect("index.php?p=search&error");
    }

    redirect("index.php?p=search");
?>