<?php
    if(is_logged_in() and has_a_priority(3))
    {
        if(isset($_POST['setname']) and isset($_POST['isactive']) and isset($_POST['description']) and isset($_POST['publish_time']))
        {
            if($_POST['isactive']=="1")
            {
                $isarchived = 0;
            } else {
                $isarchived = 1;
            }

            if(isset($_FILES["set_img"]) && $_FILES["set_img"]["error"] === UPLOAD_ERR_OK) {
                $image = load_img_input($_FILES["set_img"], "../img/problemsets/header/");
            } else {
                $image = load_img_input();
            }

            $db_query = $pdo->prepare('INSERT INTO PROBLEMSETS (title, author_id, description, publish_time, isarchived, img_path) VALUES (:title, :aid, :desc, :publishtime, :isarchived, :img_path)');
            $db_query->execute([
                'title' => $_POST['setname'], 
                'aid' => $_SESSION['AUTH_ID'],
                'desc'=> $_POST['description'],
                'publishtime' => $_POST['publish_time'],
                'img_path' => $image,
                'isarchived' => $isarchived
            ]);

            redirect("index.php?p=sets");
        } else {
            redirect("index.php?p=sets&error");
        }
    } else {
        kick();
    }
?>