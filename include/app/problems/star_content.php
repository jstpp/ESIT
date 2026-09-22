<?php
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if(!has_permission('main.social.star_content') || !$id) kick();
    try {
        $pdo->beginTransaction();
        $db_query = $pdo->prepare('SELECT * FROM STARS WHERE user_id=:uid AND content_id=:cid');
        $db_query->execute(['uid' => $_SESSION['AUTH_ID'], 'cid' => $id]);
        if($row = $db_query->fetch()) {
            $db_query = $pdo->prepare('DELETE FROM STARS WHERE user_id=:uid AND content_id=:cid');
        } else {
            $db_query = $pdo->prepare('INSERT INTO STARS (user_id, content_id) VALUES (:uid, :cid)');
        }
        $db_query->execute(['uid' => $_SESSION['AUTH_ID'], 'cid' => $id]);
        $pdo->commit();
    } catch (Throwable $t) {
        if($pdo->inTransaction()) $pdo->rollback();
        extended_exception_handler($t);
        redirect("index.php?p=channels&error");
    }
    redirect("/app/index.php?p=content&id=".$id);
?>