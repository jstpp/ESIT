<?php
	if(!is_logged_in() || !has_a_priority(3) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) kick();
	if(empty(trim($_POST['fname'])) || empty(trim($_POST['fareahidden']))) redirect("index.php?p=portal&error");
	try 
	{
		$db_query = $pdo->prepare('UPDATE ARTICLES SET title=:ptitle, content=:pcontent WHERE id=:pid');
		$db_query->execute(['ptitle' => trim($_POST['fname']), 'pcontent' => $_POST['fareahidden'], 'pid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);
		redirect("index.php?p=portal");
	} catch (Throwable $t) {
		extended_exception_handler($t);
		redirect("index.php?p=portal&error");
	}
?>