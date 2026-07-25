<?php
    if(!is_logged_in() || !has_a_priority(3)) kick();
    if(!isset($_POST['setname'], $_POST['description'], $_POST['publish_time'])) redirect("index.php?p=channels&error");

    $isarchived = (isset($_POST['isactive']) && $_POST['isactive'] === '1') ? 0 : 1;
    
    if(isset($_FILES["set_img"]) && $_FILES["set_img"]["error"] === UPLOAD_ERR_OK) {
        $image = load_img_input($_FILES["set_img"], "../img/problemsets/header");
    } else {
        $image = load_img_input();
    }

    $title = trim($_POST['setname']);
    if(empty($title)) redirect("index.php?p=channels&error");
    
    try {
        $db_query = $pdo->prepare('INSERT INTO CHANNELS (title, author_id, description, publish_time, isarchived, img_path) VALUES (:title, :aid, :desc, :publishtime, :isarchived, :img_path)');
        $db_query->execute([
            'title' => $title, 
            'aid' => $_SESSION['AUTH_ID'],
            'desc'=> trim($_POST['description']),
            'publishtime' => $_POST['publish_time'],
            'img_path' => $image,
            'isarchived' => $isarchived
        ]);
    } catch (Throwable) {
        redirect("index.php?p=channels&error");
    }

    redirect("index.php?p=channels");
?>