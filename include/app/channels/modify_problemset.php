<?php
    if(!is_logged_in() || !has_a_priority(3)) kick();
    if(!isset($_GET['sid'], $_POST['update_block_form_name'], $_POST['update_block_form_condition'], $_POST['update_block_form_publish_time'])) redirect("index.php?p=channels&error");
    
    $sid = filter_var($_GET['sid'] ?? null, FILTER_VALIDATE_INT);
    if ($sid===False || $sid <= 0) redirect("index.php?p=channels&error");

    try {
        switch($_POST['update_block_form_condition'][0] ?? '')
        {
            case 'S': # problemset
                $condition = json_encode(array('type' => 'problemset', 'id' => (int)mb_substr($_POST['update_block_form_condition'], 1)));
                break;
            case 'P': # problem (TBD in the future)
                $condition = json_encode(array('type' => 'problem', 'id' => (int)mb_substr($_POST['update_block_form_condition'], 1)));
                break;
            default:
                $condition = json_encode(array('type' => 'none', 'id' => 'none'));
                break;
        }

        $title = trim($_POST['update_block_form_name']);
        if(empty($title)) redirect("index.php?p=channels&error");

        $db_query = $pdo->prepare('SELECT channel_id FROM PROBLEMSETS WHERE SET_ID=:sid');
        $db_query->execute(['sid'=>$sid]);
        $cid = $db_query->fetch()['channel_id'];

        $db_query = $pdo->prepare('UPDATE PROBLEMSETS SET title=:title, depends_on=:depends_on, publish_time=:publish_time WHERE SET_ID=:sid');
        $db_query->execute([
            'title' => $title, 
            'depends_on'=> $condition,
            'publish_time' => $_POST['update_block_form_publish_time'],
            'sid' => $sid
        ]);

        redirect("index.php?p=channel&id=".$cid);
    } catch (Throwable $t) {
        redirect("index.php?p=channels&error");
    }
?>