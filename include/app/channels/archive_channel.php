<?php
    if(is_logged_in() and has_a_priority(3))
    {
        if(isset($_GET['cid']))
        {
            $db_query = $pdo->prepare('UPDATE CHANNELS SET isarchived=(isarchived+1)%2 WHERE CHANNEL_ID=:cid');
            $db_query->execute(['cid'=>$_GET['cid']]);
        } else {
            redirect("index.php?p=channels&error");
        }
        redirect("index.php?p=channel&id=".$_GET['cid']);
    } else {
        kick();
    }
?>