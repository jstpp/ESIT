<?php
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if(!has_permission('main.social.follow_channel') || !$id) kick();
    try {
        $pdo->beginTransaction();
        $db_query = $pdo->prepare('SELECT * FROM FOLLOWS WHERE user_id=:uid AND channel_id=:cid');
        $db_query->execute(['uid' => $_SESSION['AUTH_ID'], 'cid' => $id]);
        if($row = $db_query->fetch()) {
            $db_query = $pdo->prepare('DELETE FROM FOLLOWS WHERE user_id=:uid AND channel_id=:cid');
        } else {
            $db_query = $pdo->prepare('INSERT INTO FOLLOWS (user_id, channel_id) VALUES (:uid, :cid)');
        }
        $db_query->execute(['uid' => $_SESSION['AUTH_ID'], 'cid' => $id]);
        $pdo->commit();
    } catch (Throwable $t) {
        if($pdo->inTransaction()) $pdo->rollback();
        extended_exception_handler($t);
        redirect("index.php?p=channels&error");
    }
    redirect("/app/index.php?p=channel&id=".$id);
?>