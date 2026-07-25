<?php
    if(!is_logged_in() || !has_a_priority(3)) kick();
    if(!isset($_GET['cid'], $_POST['new_block_form_name'], $_POST['new_block_form_condition'], $_POST['new_block_form_publish_time'])) redirect("index.php?p=channels&error");
    
    $cid = filter_var($_GET['cid'] ?? null, FILTER_VALIDATE_INT);
    if ($cid===False || $cid<=0) redirect("index.php?p=channels&error");
    
    try {
        switch($_POST['new_block_form_condition'][0] ?? '')
        {
            case 'S': # problemset
                $condition = json_encode(array('type' => 'problemset', 'id' => (int)mb_substr($_POST['new_block_form_condition'], 1)));
                break;
            case 'P': # problem (TBD in the future)
                $condition = json_encode(array('type' => 'problem', 'id' => (int)mb_substr($_POST['new_block_form_condition'], 1)));
                break;
            default:
                $condition = json_encode(array('type' => 'none', 'id' => 'none'));
                break;
        }

        $title = trim($_POST['new_block_form_name']);
        if (empty($title)) redirect("index.php?p=channels&error");

        $db_query = $pdo->prepare('INSERT INTO PROBLEMSETS (title, author_id, channel_id, depends_on, publish_time) VALUES (:title, :author, :channel, :depends_on, :publish_time)');
        $db_query->execute([
            'title' => $title, 
            'author' => $_SESSION['AUTH_ID'],
            'channel' => $cid,
            'depends_on'=> $condition,
            'publish_time' => $_POST['new_block_form_publish_time']
        ]);
        $setid = $pdo->lastInsertId();

        $db_query = $pdo->prepare('SELECT layout FROM CHANNELS WHERE CHANNEL_ID=:cid');
        $db_query->execute(['cid' => $cid]);
        $layout = json_decode($db_query->fetch()['layout'] ?? '[]', true) ?: ['content' => []];
        if (!isset($layout['content']) || !is_array($layout['content'])) $layout['content'] = [];
        array_push($layout['content'], array('type' => 'problemset', 'id' => $setid, 'content' => []));
        $db_query = $pdo->prepare('UPDATE CHANNELS SET layout=:layout WHERE CHANNEL_ID=:cid');
        $db_query->execute(['layout' => json_encode($layout), 'cid' => $cid]);

        redirect("index.php?p=channel&id=".$cid);

    } catch (Throwable $t) {
        redirect("index.php?p=channels&error");
    }
?>