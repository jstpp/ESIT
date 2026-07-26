<?php
    if(!is_logged_in() || !has_a_priority(3)) kick();
    if(!isset($_GET['cid']) || filter_var($_GET['cid'], FILTER_VALIDATE_INT)<=0) redirect("index.php?p=channels&error");

    try
    {
        $db_query = $pdo->prepare('UPDATE CHANNELS SET isarchived=(isarchived+1)%2 WHERE CHANNEL_ID=:cid');
        $db_query->execute(['cid'=>$_GET['cid']]);
        if ($db_query->rowCount() === 0) redirect("index.php?p=channels&error");
        redirect("index.php?p=channel&id=".$_GET['cid']);
    } catch (Throwable $t) {
        extended_exception_handler($t);
        redirect("index.php?p=channels&error");
    }
?>