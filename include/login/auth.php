<?php
	if(!isset($_POST['auth_username_or_mail']) || !isset($_POST['auth_password'])) 
	{
		redirect('index.php?response=failed');
	}

	$db_query = $pdo->prepare('SELECT * FROM USERS WHERE username=:usr1 OR mail=:usr2');
    $db_query->execute(['usr1' => $_POST['auth_username_or_mail'], 'usr2' => $_POST['auth_username_or_mail']]);

	if($row = $db_query->fetch())
	{
		if(password_verify($_POST['auth_password'], $row['password']))
		{
			session_regenerate_id(true);
			$_SESSION['AUTH_ID'] = $row['USER_ID'];
			$_SESSION['AUTH_USERNAME'] = $row['username'];
			$_SESSION['AUTH_NAME'] = $row['name'];
			$_SESSION['AUTH_SURNAME'] = $row['surname'];
			$_SESSION['AUTH_MAIL'] = $row['mail'];
			$_SESSION['AUTH_LAST_LOGIN'] = $row['lastlogin'];
			$_SESSION['SESSION_TIMEOUT'] = time()+18000;
			$_SESSION['AUTH_ROLE'] = get_roles($row['USER_ID'])[0];
			
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