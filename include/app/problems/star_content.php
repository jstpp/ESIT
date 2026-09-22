<?php
    if(!has_permission('main.social.star_content') || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) kick();
    try {
        $pdo->beginTransaction();
        if(in_array($_GET['id'], $_SESSION['CONTENT_STARS'])) 
        {
            unset($_SESSION['CONTENT_STARS'][array_search($_GET['id'], $_SESSION['CONTENT_STARS'])]);
            $db_query = $pdo->prepare('UPDATE CONTENT SET stars=(stars-1) WHERE CONTENT_ID=:cid');
            $db_query->execute(['cid' => $_GET['id']]);
        } else {
            array_push($_SESSION['CONTENT_STARS'], $_GET['id']);
            $db_query = $pdo->prepare('UPDATE CONTENT SET stars=(stars+1) WHERE CONTENT_ID=:cid');
            $db_query->execute(['cid' => $_GET['id']]);
        }
        $db_query = $pdo->prepare('UPDATE USERS SET stars=:stars WHERE USER_ID=:uid');
        $db_query->execute(['stars' => json_encode(Array('stars'=>$_SESSION['CONTENT_STARS'])),'uid' => $_SESSION['AUTH_ID']]);
        $pdo->commit();
    } catch (Throwable $t) {
        if($pdo->inTransaction()) $pdo->rollback();
        extended_exception_handler($t);
        redirect("index.php?p=channels&error");
    }
    redirect("/app/index.php?p=content&id=".$_GET['id']);
?>