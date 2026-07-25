<?php
    if(is_logged_in() and has_a_priority(3))
    {
        if(isset($_GET['cid']) and isset($_POST['new_block_form_name']) and isset($_POST['new_block_form_condition']) and isset($_POST['new_block_form_publish_time']))
        {   
            $cid = filter_var($_GET['cid'] ?? null, FILTER_VALIDATE_INT);
            if ($cid===False) redirect("index.php?p=channels&error");
            try {
                switch($_POST['new_block_form_condition'][0])
                {
                    case 'S': # problemset
                        $condition = json_encode(array('type' => 'problemset', 'id' => (int)mb_substr($_POST['new_block_form_condition'], 1)));
                        break;
                    case 'P': # problem
                        $condition = json_encode(array('type' => 'problem', 'id' => (int)mb_substr($_POST['new_block_form_condition'], 1)));
                        break;
                    default:
                        $condition = json_encode(array('type' => 'none', 'id' => 'none'));
                        break;
                }
                $db_query = $pdo->prepare('INSERT INTO PROBLEMSETS (title, author_id, channel_id, depends_on, publish_time) VALUES (:title, :author, :channel, :depends_on, :publish_time)');
                $db_query->execute([
                    'title' => $_POST['new_block_form_name'], 
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