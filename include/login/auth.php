<?php
	if(!isset($_POST['auth_username']) || !isset($_POST['auth_password'])) 
	{
		redirect('index.php?response=failed');
	}

	$db_query = $pdo->prepare('SELECT * FROM USERS WHERE username=:usr');
    $db_query->execute(['usr' => $_POST['auth_username']]);

	if($row = $db_query->fetch())
	{
		if(password_verify($_POST['auth_password'], $row['password']))
		{
			session_regenerate_id(true);
			$_SESSION['AUTH_ID'] = $row['USER_ID'];
			$_SESSION['AUTH_USERNAME'] = $row['username'];
			$_SESSION['AUTH_NAME'] = $row['name'];
			$_SESSION['AUTH_SURNAME'] = $row['surname'];
			$_SESSION['AUTH_LEVEL'] = $row['role'];
			$_SESSION['AUTH_MAIL'] = $row['mail'];
			$_SESSION['AUTH_LAST_LOGIN'] = $row['lastlogin'];
			$_SESSION['SESSION_TIMEOUT'] = time()+18000;

			if($row['role']==1)
			{
				$_SESSION['AUTH_ROLE'] = "administrator";
			} else if($row['role']==5)
			{
				$_SESSION['AUTH_ROLE'] = "nauczyciel";
			} else
			{
				$_SESSION['AUTH_ROLE'] = "użytkownik";
			}
			
			$db_query = $pdo->prepare('UPDATE USERS SET lastlogin=:lastlogin WHERE USER_ID=:uid');
    		$db_query->execute(['lastlogin' => date('Y/m/d H:i:s'), 'uid' => $_SESSION['AUTH_ID']]);

			redirect('../app');
		} else {
			redirect('index.php?response=failed');
		}
	} else {
		redirect('index.php?response=failed'); 
	}
?>