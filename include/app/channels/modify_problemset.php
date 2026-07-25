<?php
    if(is_logged_in() and has_a_priority(3))
    {
        if(isset($_GET['sid']) and isset($_POST['update_block_form_name']) and isset($_POST['update_block_form_condition']) and isset($_POST['update_block_form_publish_time']))
        {   
            $sid = filter_var($_GET['sid'] ?? null, FILTER_VALIDATE_INT);
            if ($sid===False) redirect("index.php?p=channels&error");
            try {
                switch($_POST['update_block_form_condition'][0])
                {
                    case 'S': # problemset
                        $condition = json_encode(array('type' => 'problemset', 'id' => (int)mb_substr($_POST['update_block_form_condition'], 1)));
                        break;
                    case 'P': # problem
                        $condition = json_encode(array('type' => 'problem', 'id' => (int)mb_substr($_POST['update_block_form_condition'], 1)));
                        break;
                    default:
                        $condition = json_encode(array('type' => 'none', 'id' => 'none'));
                        break;
                }
                $db_query = $pdo->prepare('UPDATE PROBLEMSETS SET title=:title, depends_on=:depends_on, publish_time=:publish_time WHERE SET_ID=:sid');
                $db_query->execute([
                    'title' => $_POST['update_block_form_name'], 
                    'depends_on'=> $condition,
                    'publish_time' => $_POST['update_block_form_publish_time'],
                    'sid' => $sid
                ]);

                $db_query = $pdo->prepare('SELECT channel_id FROM PROBLEMSETS WHERE SET_ID=:sid');
                $db_query->execute(['sid'=>$sid]);
                $cid = $db_query->fetch()['channel_id'];
            } catch (Throwable $t) {
                redirect("index.php?p=channels&error");
            }
            redirect("index.php?p=channel&id=".$cid);
        } else {
            redirect("index.php?p=channels&error");
        }
    } else {
        kick();
    }
?>