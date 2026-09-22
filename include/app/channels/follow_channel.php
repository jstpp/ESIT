<?php
    if(!has_permission('main.social.follow_channel') || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) kick();
    try {
        $pdo->beginTransaction();
        if(in_array($_GET['id'], $_SESSION['CONTENT_FOLLOWS'])) 
        {
            unset($_SESSION['CONTENT_FOLLOWS'][array_search($_GET['id'], $_SESSION['CONTENT_FOLLOWS'])]);
            $db_query = $pdo->prepare('UPDATE CHANNELS SET followers=(followers-1) WHERE CHANNEL_ID=:cid');
            $db_query->execute(['cid' => $_GET['id']]);
        } else {
            array_push($_SESSION['CONTENT_FOLLOWS'], $_GET['id']);
            $db_query = $pdo->prepare('UPDATE CHANNELS SET followers=(followers+1) WHERE CHANNEL_ID=:cid');
            $db_query->execute(['cid' => $_GET['id']]);
        }
        $db_query = $pdo->prepare('UPDATE USERS SET follows=:follows WHERE USER_ID=:uid');
        $db_query->execute(['follows' => json_encode(Array('follows'=>$_SESSION['CONTENT_FOLLOWS'])),'uid' => $_SESSION['AUTH_ID']]);
        $pdo->commit();
    } catch (Throwable $t) {
        if($pdo->inTransaction()) $pdo->rollback();
        extended_exception_handler($t);
        redirect("index.php?p=channels&error");
    }
    redirect("/app/index.php?p=channel&id=".$_GET['id']);
?>