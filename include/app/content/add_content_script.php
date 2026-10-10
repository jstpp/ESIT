<?php
    function process_content_create_root($cid)
	{
        try {
            mkdir(__DIR__.'/../../../include/storage/video/'.(int)$cid, 0777, true);
        } catch (Throwable $t) {
            extended_exception_handler($t);
            return False;
        }
        return True;
	}

    function process_content_check($file, $mode)
	{
        try {
            $restricted_extensions = ['php', 'php3', 'php4', 'php5', 'phtml', 'pl', 'cgi', '.htaccess'];
            $file_extension = strtolower(pathinfo(basename($file["name"]),PATHINFO_EXTENSION));
            $check = filesize($file["tmp_name"]);

            if (in_array($file_extension, $restricted_extensions, true)) return False;
            if ($file_extension != $mode ) return False;

            return True;
        } catch (Throwable $t) {
            extended_exception_handler($t);
            return False;
        }
	}

    function process_content_save($file, $mode, $cid)
	{
        $cid = (int)$cid;
        try {
            $index = "new_file";
            $target_file = __DIR__.'/../../../include/storage/video/'.$cid."/".basename($file["name"]);
            $file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            $target_file = __DIR__.'/../../../include/storage/video/'.$cid."/".$cid.".".$file_type;
            $check = filesize($file["tmp_name"]);

            if (file_exists($target_file)) throw new Exception("File already exists.");
            if (!move_uploaded_file($file["tmp_name"], $target_file)) throw new Exception("Moving file unsuccessful");

            return $target_file;
        } catch (Throwable $t) {
            extended_exception_handler($t);
            return null;
        }
	}


    $sid = filter_var($_GET['sid'] ?? null, FILTER_VALIDATE_INT);
    if(!has_permission('main.channels.add_problem') || !$sid) kick();
    if(!isset($_POST['content_type'], $_FILES['vid_file']) || $_POST['content_type']<=5 || $_POST['content_type']>6) kick();
    
    try
    {
        $pdo->beginTransaction();

        $db_query = $pdo->prepare('INSERT INTO CONTENT (title, author_id, type, maxattempts, maxpoints, problemset, result_publish_time, publish_time, isarchived, comment) VALUES (:title, :aid, :type, :maxattempts, :maxpoints, :sid, :resultpublishtime, :publishtime, :isarchived, :comment)');
        $db_query->execute([
            'title' => trim($_POST['content_title']), 
            'aid' => $_SESSION['AUTH_ID'], 
            'type' => filter_var($_POST['content_type'], FILTER_VALIDATE_INT), 
            'maxattempts' => 1, 
            'maxpoints' => 0, 
            'sid' => $sid, 
            'resultpublishtime' => $_POST['publish_time'], 
            'publishtime' => $_POST['publish_time'], 
            'isarchived' => filter_var($_POST['content_isarchived'], FILTER_VALIDATE_INT),
            'comment' => 'https://stream.mux.com/BV3YZtogl89mg9VcNBhhnHm02Y34zI1nlMuMQfAbl3dM/highest.mp4'
        ]);
        $content_id = $pdo->lastInsertId() ?? null;
    } catch (Throwable $t){
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        extended_exception_handler($t);
        redirect("index.php?p=channels&error");
    }

    if(!$content_id) redirect("index.php?p=channels&error");
    
    try
    {
        if(!process_content_create_root($content_id)) throw new Exception("process_content_create_root() failed");
        if(!process_content_check($_FILES['vid_file'], 'mp4')) throw new Exception("process_content_check() failed");
        $saved_file_path = process_content_save($_FILES['vid_file'], 'mp4', $content_id);
    } catch (Throwable $t){
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if (file_exists(delete_directory(__DIR__.'/../../../include/storage/video/'.$content_id."/"))) 
        {
            delete_directory(__DIR__.'/../../../include/storage/video/'.$content_id."/");
        }
        extended_exception_handler($t);
        redirect("index.php?p=channels&error");
    }

    try {
        $db_query = $pdo->prepare('UPDATE CONTENT SET comment=:comment WHERE CONTENT_ID=:cid');
        $db_query->execute([
            'comment' => $saved_file_path, 
            'cid' => $content_id
        ]);
        $pdo->commit();
    } catch (Throwable $t){
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if (file_exists(delete_directory(__DIR__.'/../../../include/storage/video/'.$content_id."/"))) 
        {
            delete_directory(__DIR__.'/../../../include/storage/video/'.$content_id."/");
        }
        extended_exception_handler($t);
        redirect("index.php?p=channels&error");
    }

    redirect('index.php?p=content&id='.filter_var($content_id, FILTER_VALIDATE_INT));
?>