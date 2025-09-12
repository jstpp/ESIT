<?php
    function process_check_img($file)
	{
		$FileType = strtolower(pathinfo(basename($file["name"]),PATHINFO_EXTENSION));
		$check = filesize($file["tmp_name"]);

        if($check !== false) {
          	echo "File - OK";
          	$uploadOk = 1;
        } else {
          	echo "Not a file";
          	$uploadOk = 0;
        }

		if (file_exists(basename($file["name"]))) {
          	echo "Sorry, file already exists.";
          	$uploadOk = 0;
        }

		if ($file["size"] > 200000000) {
        	echo "Plik zajmuje zbyt wiele miejsca!";
        	$uploadOk = 0;
        }

        if (!in_array(strtolower($FileType), array("gif", "jpg", "png", "webp", "jpeg"))) {
            echo "Niedozwolony format pliku. Tylko: gif, jpg, png, webp, jpeg";
            $uploadOk = 0;
        }

		if ($uploadOk == 0) {
          	return False;
        } else {
          	return True;
        }
	}

    function process_check($file, $mode)
	{
		$FileType = strtolower(pathinfo(basename($file["name"]),PATHINFO_EXTENSION));
		$check = filesize($file["tmp_name"]);

        if($check !== false) {
          	echo "File - OK";
          	$uploadOk = 1;
        } else {
          	echo "Not a file";
          	$uploadOk = 0;
        }

		if (file_exists(basename($file["name"]))) {
          	echo "Sorry, file already exists.";
          	$uploadOk = 0;
        }

		if ($file["size"] > 20000000) {
        	echo "Plik zajmuje zbyt wiele miejsca!";
        	$uploadOk = 0;
        }

		if($FileType != $mode ) {
          echo "Tylko pliki .".htmlentities($mode)." są dozwolone.";
          $uploadOk = 0;
        }

		if ($uploadOk == 0) {
          	return False;
        } else {
          	return True;
        }
	}

    function process_resource($file, $mode, $rid, $org_link)
	{
        mkdir("../include/resources/".$rid."/", 0777);

        $target_file = "../include/resources/".$rid."/".basename($file["name"]);
		$FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		$target_file = "../include/resources/".$rid."/".$rid.".".$FileType;
		$check = filesize($file["tmp_name"]);

        $uploadOk = 1;
		if (file_exists($target_file)) {
          	echo "Sorry, file already exists.";
          	$uploadOk = 0;
        }

		if ($uploadOk == 0) {
          	echo "<br/>Przepraszamy, wystąpił błąd przy wysyłaniu. Zgłoś to administracji.";
        } else {
          	if (move_uploaded_file($file["tmp_name"], $target_file)) {
            	echo("<= OK =>");
          	} else {
            	echo "<br/>Przepraszamy, wystąpił błąd przy wysyłaniu. Zgłoś to administracji.";
          	}
        }

        $target_file = $org_link."/include/resources/".$rid."/".$rid.".".$FileType;

		return $target_file;
	}

    if(isset($_GET['mode']) and is_logged_in() and has_a_priority(3))
    {
        if($_GET['mode']=="quests")
        {
            $i = 1;
            while(isset($_FILES['quest_file_'.$i]) and isset($_POST['quest_name_'.$i]))
            {
                $db_query = $pdo->prepare('INSERT INTO PORTAL_RESOURCES (resource_type, resource_name, resource_path) VALUES ("quests", :rname, :rpath)');
			    $db_query->execute(['rname' => $_POST['quest_name_'.$i], 'rpath' => '-']);
                $resource_id = $pdo->lastInsertId();

                if(process_check($_FILES['quest_file_'.$i], "pdf"))
                {
                    $resource_path = process_resource($_FILES['quest_file_'.$i], "pdf", $resource_id, get_misc_value('general_url'));
                    if($_POST['is_actual_'.$i]=="1")
                    {
                        $is_actual = 1;
                    } else {
                        $is_actual = 0;
                    }
                } else {
                    kick();
                }

                $db_query = $pdo->prepare('UPDATE PORTAL_RESOURCES SET resource_path=:rpath, is_actual=:ia WHERE RESOURCE_ID=:rid');
			    $db_query->execute(['rid' => $resource_id, 'rpath' => $resource_path, 'ia' => $is_actual]);
                $i++;
            }
        } else if ($_GET['mode']=="docs") {
            $i = 1;
            while(isset($_FILES['document_file_'.$i]) and isset($_POST['document_name_'.$i]))
            {
                $db_query = $pdo->prepare('INSERT INTO PORTAL_RESOURCES (resource_type, resource_name, resource_path) VALUES ("documents", :rname, :rpath)');
			    $db_query->execute(['rname' => $_POST['document_name_'.$i], 'rpath' => '-']);
                $resource_id = $pdo->lastInsertId();

                if(process_check($_FILES['document_file_'.$i], "pdf"))
                {
                    $resource_path = process_resource($_FILES['document_file_'.$i], "pdf", $resource_id, $org_link);
                } else {
                    kick();
                }

                $db_query = $pdo->prepare('UPDATE PORTAL_RESOURCES SET resource_path=:rpath WHERE RESOURCE_ID=:rid');
			    $db_query->execute(['rpath' => $resource_path, 'rid' => $resource_id]);
                $i++;
            }
        } else if ($_GET['mode']=="logo") {
            $i = 1;
            while(isset($_FILES['logo_file_'.$i]) and isset($_POST['logo_name_'.$i]) and isset($_POST['logo_href_'.$i]))
            {
                $db_query = $pdo->prepare('INSERT INTO PORTAL_RESOURCES (resource_type, resource_name, resource_path, resource_comment) VALUES ("logo", :rname, :rpath, :rcomment)');
			    $db_query->execute(['rname' => $_POST['logo_name_'.$i], 'rpath' => '-', 'rcomment' => $_POST['logo_href_'.$i]]);
                $resource_id = $pdo->lastInsertId();

                if(process_check_img($_FILES['logo_file_'.$i]))
                {
                    $resource_path = process_resource($_FILES['logo_file_'.$i], strtolower(pathinfo(basename($_FILES['logo_file_'.$i]['name']),PATHINFO_EXTENSION)), $resource_id, get_misc_value('general_url'));
                } else {
                    kick();
                }

                $db_query = $pdo->prepare('UPDATE PORTAL_RESOURCES SET resource_path=:rpath WHERE RESOURCE_ID=:rid');
			    $db_query->execute(['rpath' => $resource_path, 'rid' => $resource_id]);
                $i++;
            }
        } else if ($_GET['mode']=="terms") {
            $db_query = $pdo->prepare('DELETE FROM TERMS');
			$db_query->execute();

            $m = 1;
            while(isset($_POST['term_name_'.$m]))
            {
                if(strlen($_POST['term_name_'.$m])>1)
                {
                    try {
                        $db_query = $pdo->prepare('INSERT INTO TERMS (term_name, term_begin, term_end) VALUES (:tname, :tb, :te)');
                        $db_query->execute(['tname' => $_POST['term_name_'.$m], 'tb' => $_POST['term_begin_'.$m], 'te' => $_POST['term_end_'.$m]]);
                    } catch (Exception $e) {
                        echo($e);
                        die;
                    }
                }
                $m++;
            }
        } else if ($_GET['mode']=="socialmedia") {
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

        } else if ($_GET['mode']=="general") {
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

        } else if ($_GET['mode']=="remove" and isset($_GET['rid'])) {
            $db_query = $pdo->prepare('DELETE FROM PORTAL_RESOURCES WHERE RESOURCE_ID=:rid');
			$db_query->execute(['rid' => $_GET['rid']]);
        } else if ($_GET['mode']=="archive" and isset($_GET['rid']) and isset($_GET['rnew'])) {
            if($_GET['rnew']=="1")
            {
                $is_actual = 1;
            } else {
                $is_actual = 0;
            }
            $db_query = $pdo->prepare('UPDATE PORTAL_RESOURCES SET is_actual=:ia WHERE RESOURCE_ID=:rid');
			$db_query->execute(['ia' => $is_actual, 'rid' => $_GET['rid']]);
        } else {
            kick();
        }
    } else {
        kick();
    }

    redirect("index.php?p=portal");
    die;
?>