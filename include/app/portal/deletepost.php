<?php
	try 
	{
		if(has_a_priority(3))
		{
			try {
				$db_query = $pdo->prepare('SELECT * FROM ARTICLES WHERE id=:pid');
				$db_query->execute(['pid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);

				while($row = $db_query->fetch())
				{
					$headerimagename = $row['image_path'];
				}

				$db_query = $pdo->prepare('DELETE FROM ARTICLES WHERE id=:pid');
				$db_query->execute(['pid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);

				if(file_exists($headerimagename) and $headerimagename!="img/placeholder.jpeg")
				{
					unlink($headerimagename);
				}

				header("Location: index.php?p=portal");
			} catch (Exception $e) {
				kick();
			}
		} else {
			kick();
		}
	} catch (Exception $e) {
		kick();
	}
?>