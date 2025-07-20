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

            $db_query = $pdo->prepare('INSERT INTO PROBLEMSETS (title, author_id, description, publish_time, isarchived) VALUES (:title, :aid, :desc, :publishtime, :isarchived)');
            $db_query->execute([
                'title' => $_POST['setname'], 
                'aid' => $_SESSION['AUTH_ID'],
                'desc'=> $_POST['description'],
                'publishtime' => $_POST['publish_time'],
                'isarchived' => $isarchived
            ]);

            header("Location: index.php?p=sets");
            die;
        } else {
            header("Location: index.php?p=sets&error");
            die;
        }
    } else {
        kick();
    }
?>