<?php
	function processimage($img)
	{
		$index = "new_img";
		$target_file = "../img/articles/header/".basename($img["name"]);
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		$target_file = "../img/articles/header/".$index.".".$imageFileType;
		$check = getimagesize($img["tmp_name"]);

        if($check !== false) {
          	echo "File is an image - " . $check["mime"] . ".";
          	$uploadOk = 1;
        } else {
          	echo "File is not an image.";
          	$uploadOk = 0;
        }

		if (file_exists($target_file)) {
          	echo "Sorry, file already exists.";
          	$uploadOk = 0;
        }

		if ($img["size"] > 1000000) {
        	echo "Obraz okładki zajmuje zbyt wiele miejsca!";
        	$uploadOk = 0;
        }

		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "webp" ) {
          echo "Tylko pliki JPG, JPEG, PNG i WEBP są dozwolone.";
          $uploadOk = 0;
        }

		if ($uploadOk == 0) {
          	echo "<br/>Przepraszamy, wystąpił błąd przy wysyłaniu artykułu. Zgłoś to administracji.";
        } else {
          	if (move_uploaded_file($img["tmp_name"], $target_file)) {
            	echo "<center><br />Okładka o nazwie ". htmlspecialchars(basename($img["name"])). " została pomyślnie zapisana w systemie.</center>";
          	} else {
            	echo "<br/>Przepraszamy, wystąpił błąd przy wysyłaniu artykułu. Zgłoś to administracji.";
          	}
        }

		return $target_file;
	}

	try 
	{
		if(has_a_priority(3))
		{
			$db_query = $pdo->prepare('INSERT INTO ARTICLES (title, author, content, image_path) VALUES (:ptitle, :pauthor, :pcontent, :pimage)');
			$db_query->execute(['ptitle' => $_POST['fname'], 'pauthor' => $_SESSION['AUTH_USERNAME'], 'pcontent' => $_POST['fareahidden'], 'pimage' => "img/placeholder.jpeg"]);

			$db_query = $pdo->prepare('SELECT MAX(id) AS maxid FROM ARTICLES');
			$db_query->execute();
			$maxid = $db_query->fetch()['maxid'];
			$oldpath = processimage($_FILES["fimage"]);

			rename($oldpath, "../img/articles/header/".$maxid.".".strtolower(pathinfo($oldpath,PATHINFO_EXTENSION)));

			$db_query = $pdo->prepare('UPDATE ARTICLES SET image_path=:pimage WHERE id=:pid');
			$db_query->execute(['pimage' => "img/articles/header/".$maxid.".".strtolower(pathinfo($oldpath,PATHINFO_EXTENSION)), 'pid' => $maxid]); #To wszystko trzeba uprościć...

			redirect("index.php?p=portal");
		} else {
			kick();
		}
	} catch (Exception $e) {
		kick();
	}
?>