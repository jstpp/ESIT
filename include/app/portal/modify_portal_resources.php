<?php
    function process_check_img($file)
	{
        try {
            $FileType = strtolower(pathinfo(basename($file["name"]),PATHINFO_EXTENSION));
            $check = filesize($file["tmp_name"]);

            $uploadOk = ($check !== False) ? 1 : 0;
            if ($file["size"] > 200000000) $uploadOk = 0;
            if (!in_array(strtolower($FileType), array("gif", "jpg", "png", "webp", "jpeg"))) $uploadOk = 0;

            return ($uploadOk == 0) ? False : True;
        } catch (Throwable $t) {
            extended_exception_handler($t);
		    return False;
        }
	}

    function process_check($file, $mode)
	{
        try {
            $FileType = strtolower(pathinfo(basename($file["name"]),PATHINFO_EXTENSION));
            $check = filesize($file["tmp_name"]);

            $uploadOk = ($check !== False) ? 1 : 0;
            if ($file["size"] > 200000000) $uploadOk = 0;
            if ($FileType != $mode ) $uploadOk = 0;

            return ($uploadOk == 0) ? False : True;
        } catch (Throwable $t) {
            extended_exception_handler($t);
		    return False;
        }
	}

    function process_resource($file, $mode, $rid, $org_link)
	{
        try {
            if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) return "";
            mkdir("../include/resources/".$rid."/", 0777, true);

            $target_file = "../include/resources/".$rid."/".basename($file["name"]);
            $FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            $target_file = "../include/resources/".$rid."/".$rid.".".$FileType;
            $check = filesize($file["tmp_name"]);

            $uploadOk = (file_exists($target_file)) ? 0 : 1;
            if ($file["size"] > 200000000) $uploadOk = 0;
            if ($FileType != $mode ) $uploadOk = 0;

            if ($uploadOk == 0) return "";
            if (!move_uploaded_file($file["tmp_name"], $target_file)) return "";

            $target_file = $org_link."/include/resources/".$rid."/".$rid.".".$FileType;
            return $target_file;
        } catch (Throwable $t) {
            extended_exception_handler($t);
		    return "";
        }
	}
    

    if(!isset($_GET['mode']) || !has_a_priority(3)) kick();
    $org_link = get_misc_value("general_url");
    
    try {
        if($_GET['mode']=="quests")
        {
            $i = 1;
            $pdo->beginTransaction();
            while(isset($_FILES['quest_file_'.$i]) and isset($_POST['quest_name_'.$i]))
            {
                if(!process_check($_FILES['quest_file_'.$i], "pdf")) break;
                $db_query = $pdo->prepare('INSERT INTO PORTAL_RESOURCES (resource_type, resource_name, resource_path) VALUES ("quests", :rname, :rpath)');
                $db_query->execute(['rname' => $_POST['quest_name_'.$i], 'rpath' => '-']);
                $resource_id = $pdo->lastInsertId();
                $resource_path = process_resource($_FILES['quest_file_'.$i], "pdf", $resource_id, get_misc_value('general_url'));
                if(strlen($resource_path)<2) throw new Exception("Invalid resource path");
                $is_actual = (isset($_POST['is_actual_'.$i])) ? 1 : 0;
                $db_query = $pdo->prepare('UPDATE PORTAL_RESOURCES SET resource_path=:rpath, is_actual=:ia WHERE RESOURCE_ID=:rid');
                $db_query->execute(['rid' => $resource_id, 'rpath' => $resource_path, 'ia' => $is_actual]);
                $i++;
            }
            $pdo->commit();
        } else if ($_GET['mode']=="docs") {
            $i = 1;
            $pdo->beginTransaction();
            while(isset($_FILES['document_file_'.$i]) and isset($_POST['document_name_'.$i]))
            {
                if(!process_check($_FILES['document_file_'.$i], "pdf")) break;
                $db_query = $pdo->prepare('INSERT INTO PORTAL_RESOURCES (resource_type, resource_name, resource_path) VALUES ("documents", :rname, :rpath)');
                $db_query->execute(['rname' => $_POST['document_name_'.$i], 'rpath' => '-']);
                $resource_id = $pdo->lastInsertId();
                $resource_path = process_resource($_FILES['document_file_'.$i], "pdf", $resource_id, $org_link);
                if(strlen($resource_path)<2) throw new Exception("Invalid resource path");

                $db_query = $pdo->prepare('UPDATE PORTAL_RESOURCES SET resource_path=:rpath WHERE RESOURCE_ID=:rid');
                $db_query->execute(['rpath' => $resource_path, 'rid' => $resource_id]);
                $i++;
            }
            $pdo->commit();
        } else if ($_GET['mode']=="logo") {
            $i = 1;
            $pdo->beginTransaction();
            while(isset($_FILES['logo_file_'.$i]) and isset($_POST['logo_name_'.$i]) and isset($_POST['logo_href_'.$i]))
            {
                if(!process_check_img($_FILES['logo_file_'.$i])) break;
                $db_query = $pdo->prepare('INSERT INTO PORTAL_RESOURCES (resource_type, resource_name, resource_path, resource_comment) VALUES ("logo", :rname, :rpath, :rcomment)');
                $db_query->execute(['rname' => $_POST['logo_name_'.$i], 'rpath' => '-', 'rcomment' => $_POST['logo_href_'.$i]]);
                $resource_id = $pdo->lastInsertId();
                $resource_path = process_resource($_FILES['logo_file_'.$i], strtolower(pathinfo(basename($_FILES['logo_file_'.$i]['name']),PATHINFO_EXTENSION)), $resource_id, get_misc_value('general_url'));

                $db_query = $pdo->prepare('UPDATE PORTAL_RESOURCES SET resource_path=:rpath WHERE RESOURCE_ID=:rid');
                $db_query->execute(['rpath' => $resource_path, 'rid' => $resource_id]);
                $i++;
            }
            $pdo->commit();
        } else if ($_GET['mode']=="terms") {
            $pdo->beginTransaction();
            $db_query = $pdo->prepare('DELETE FROM TERMS');
            $db_query->execute();

            $i = 1;
            while(isset($_POST['term_name_'.$i]))
            {
                if(strlen($_POST['term_name_'.$i])>1)
                {
                    $db_query = $pdo->prepare('INSERT INTO TERMS (term_name, term_begin, term_end) VALUES (:tname, :tb, :te)');
                    $db_query->execute(['tname' => $_POST['term_name_'.$i], 'tb' => $_POST['term_begin_'.$i], 'te' => $_POST['term_end_'.$i]]);
                }
                $i++;
            }
            $pdo->commit();
        } else if ($_GET['mode']=="socialmedia") {
            $pdo->beginTransaction();
            $db_query = $pdo->prepare('DELETE FROM MISC WHERE misc_name LIKE "social_media_%"');
            $db_query->execute();

            if(isset($_POST['yt_href']))
            {
                $db_query = $pdo->prepare('INSERT INTO MISC (misc_name, misc_value) VALUES ("social_media_yt", :mv)');
                $db_query->execute(['mv' => $_POST['yt_href']]);
            }
            if(isset($_POST['ig_href']))
            {
                $db_query = $pdo->prepare('INSERT INTO MISC (misc_name, misc_value) VALUES ("social_media_ig", :mv)');
                $db_query->execute(['mv' => $_POST['ig_href']]);
            }
            if(isset($_POST['fb_href']))
            {
                $db_query = $pdo->prepare('INSERT INTO MISC (misc_name, misc_value) VALUES ("social_media_fb", :mv)');
                $db_query->execute(['mv' => $_POST['fb_href']]);
            }
            $pdo->commit();
        } else if ($_GET['mode']=="general") {
            $pdo->beginTransaction();
            $db_query = $pdo->prepare('DELETE FROM MISC WHERE misc_name LIKE "general_%"');
            $db_query->execute();

            if(isset($_POST['g_title']))
            {
                $db_query = $pdo->prepare('INSERT INTO MISC (misc_name, misc_value) VALUES ("general_title", :mv)');
                $db_query->execute(['mv' => $_POST['g_title']]);
            }
            if(isset($_POST['g_motd']))
            {
                $db_query = $pdo->prepare('INSERT INTO MISC (misc_name, misc_value) VALUES ("general_motd", :mv)');
                $db_query->execute(['mv' => $_POST['g_motd']]);
            }
            $pdo->commit();
        } else if ($_GET['mode']=="remove" and isset($_GET['rid'])) {
            $db_query = $pdo->prepare('DELETE FROM PORTAL_RESOURCES WHERE RESOURCE_ID=:rid');
            $db_query->execute(['rid' => filter_var($_GET['rid'], FILTER_VALIDATE_INT)]);
        } else if ($_GET['mode']=="archive" and isset($_GET['rid']) and isset($_GET['rnew'])) {
            $is_actual = (filter_var($_GET['rnew'], FILTER_VALIDATE_INT)==1) ? 1 : 0;
            $db_query = $pdo->prepare('UPDATE PORTAL_RESOURCES SET is_actual=:ia WHERE RESOURCE_ID=:rid');
            $db_query->execute(['ia' => $is_actual, 'rid' => filter_var($_GET['rid'], FILTER_VALIDATE_INT)]);
        } else {
            kick();
            die;
        }
    } catch (Throwable $t) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        extended_exception_handler($t);
        redirect("index.php?p=portal&error");
    }

    redirect("index.php?p=portal");
?>