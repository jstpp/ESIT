<?php
	try 
	{
		if($_SESSION['AUTH_LEVEL']<3)
		{
			$db_query = $pdo->prepare('UPDATE ARTICLES SET title=:ptitle, content=:pcontent WHERE id=:pid');
			$db_query->execute(['ptitle' => $_POST['fname'], 'pcontent' => $_POST['fareahidden'], 'pid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);

			header("Location: index.php?p=portal");
		} else {
			kick();
		}
	} catch (Exception $e) {
		kick();
	}
?>