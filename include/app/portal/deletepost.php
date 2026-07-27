<?php
	if(!has_a_priority(3)) kick();
	if(filter_var($_GET['id'], FILTER_VALIDATE_INT)===False || filter_var($_GET['id'], FILTER_VALIDATE_INT)===null) kick();
	try 
	{
		$db_query = $pdo->prepare('SELECT * FROM ARTICLES WHERE id=:pid');
		$db_query->execute(['pid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);
		if($row = $db_query->fetch()) $headerimagename = $row['image_path'];

		$db_query = $pdo->prepare('DELETE FROM ARTICLES WHERE id=:pid');
		$db_query->execute(['pid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);

		if(file_exists($headerimagename) and $headerimagename!="img/placeholder.jpeg") unlink("../".$headerimagename);
		redirect("index.php?p=portal");
	} catch (Throwable $t) {
		extended_exception_handler($t);
		redirect("index.php?p=portal&error");
	}
?>