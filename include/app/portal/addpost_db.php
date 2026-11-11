<?php
	if (!is_logged_in() || !has_permission('main.portal.addpost')) kick();
	try 
	{
		require_once('modules/composer/vendor/autoload.php');

		$purifier_config = HTMLPurifier_Config::createDefault();
		$purifier = new HTMLPurifier($purifier_config);

		$target_dir = "../img/articles/header";
        $uploaded_image_path = preg_replace('#^\.\.\/#', '', load_img_input($_FILES["fimage"] ?? null, $target_dir));

		$farea_content = $purifier->purify($_POST['fareahidden']);

		$db_query = $pdo->prepare('INSERT INTO ARTICLES (title, author, content, image_path) VALUES (:ptitle, :pauthor, :pcontent, :pimage)');
		$db_query->execute(['ptitle' => htmlentities($_POST['fname']), 'pauthor' => $_SESSION['AUTH_USERNAME'], 'pcontent' => $farea_content, 'pimage' => $uploaded_image_path]);

		insert_flash_message('success', __('Success').'!', __('Your article has been added successfully').'!');
		redirect("index.php?p=portal");
	} catch (Throwable $t) {
		extended_exception_handler($t);
		redirect("index.php?p=portal&error");
	}
?>