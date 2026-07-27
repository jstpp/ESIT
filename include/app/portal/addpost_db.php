<?php
	if(!is_logged_in() || !has_a_priority(3)) kick();
	try 
	{
		$target_dir = "../img/articles/header";
        $uploaded_image_path = preg_replace('#^\.\.\/#', '', load_img_input($_FILES["fimage"] ?? null, $target_dir));

		$db_query = $pdo->prepare('INSERT INTO ARTICLES (title, author, content, image_path) VALUES (:ptitle, :pauthor, :pcontent, :pimage)');
		$db_query->execute(['ptitle' => $_POST['fname'], 'pauthor' => $_SESSION['AUTH_USERNAME'], 'pcontent' => $_POST['fareahidden'], 'pimage' => $uploaded_image_path]);

		insert_flash_message('success', 'Sukces!', 'Artykuł został pomyślnie dodany.');
		redirect("index.php?p=portal");
	} catch (Throwable $t) {
		extended_exception_handler($t);
		redirect("index.php?p=portal&error");
	}
?>