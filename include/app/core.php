<?php
	###############################################
	#              general toolbox				  #
	###############################################

	function force_to_login()
	{
		global $org_link;

		header("Location: ".$org_link);

		echo('<meta http-equiv="refresh" content="0; url='.$org_link.'/login" />');
		die;
	}

	function kick()
	{
		global $error_link;
		
		header("Location: ".$error_link);
		
		echo('<meta http-equiv="refresh" content="0; url='.$error_link.'/login" />');
		die;
	}

	function redirect($dest)
	{
		echo('<meta http-equiv="refresh" content="0; url='.$dest.'" />');
		die;
	}

	function display_message($type, $header, $message)
	{
		echo('<div onClick="this.remove();" style="display: flex; justify-content: center; align-items: center; margin: 0; min-width: 100vw; min-height: 100vh; background-color: rgba(0,0,0,0.6); position: fixed; top: 0; left: 0; z-index: 999">
			<div style="background-color: #dae2e6; color: black; width: 30vmax; max-height: 60vh; padding: 1vmax 1vmax; border-radius: 0.2vmax;">');
			
		if($type=="error")
		{
			echo('<div style="margin-left: -1vmax; border-radius: 0.2vmax; margin-top: -1vmax; padding: 1vmax 1vmax; background-color: rgb(180, 80, 80); color: #dae2e6; width: 100%; text-align: center;"><h2>'.htmlentities($header).'</h2></div><br />');
		} else if($type=="warning")
		{
			echo('<div style="margin-left: -1vmax; border-radius: 0.2vmax; margin-top: -1vmax; padding: 1vmax 1vmax; background-color: rgb(180, 172, 80); color: #dae2e6; width: 100%; text-align: center;"><h2>'.htmlentities($header).'</h2></div><br />');
		} else {
			echo('<div style="margin-left: -1vmax; border-radius: 0.2vmax; margin-top: -1vmax; padding: 1vmax 1vmax; background-color: rgb(80, 143, 180); color: #dae2e6; width: 100%; text-align: center;"><h2>'.htmlentities($header).'</h2></div><br />');
		}
		echo(htmlentities($message));

		echo('<br /><br /></div>
		</div>');
	}

	function is_an_user($key)
	{
		global $pdo;
		$count = 0;

		$db_query = $pdo->prepare('SELECT COUNT(*) AS count FROM USERS WHERE username=:key');
		$db_query->execute(['key' => $key]);
		$count += $db_query->fetch()['count'];

		$db_query = $pdo->prepare('SELECT COUNT(*) AS count FROM USERS WHERE mail=:key');
		$db_query->execute(['key' => $key]);
		$count += $db_query->fetch()['count'];

		if(isset($count) and $count > 0)
		{
			return True;
		} else {
			return False;
		}
	}

	function is_logged_in()
	{
		if(isset($_SESSION['AUTH_ID']))
		{
			return True;
		} else {
			return False;
		}
	}

	function is_admin()
	{
		if(isset($_SESSION['AUTH_LEVEL']) and $_SESSION['AUTH_LEVEL']<=3)
		{
			return True;
		} else {
			return False;
		}
	}

	function has_a_priority($n)
	{
		if(isset($_SESSION['AUTH_LEVEL']) and $_SESSION['AUTH_LEVEL']<=$n)
		{
			return True;
		} else {
			return False;
		}
	}

	###############################################
	#              some automation				  #
	###############################################

	function parse_error_message($some_b64_string)
	{
		if(strlen($some_b64_string>1))
		{
			$arguments = json_decode(base64_decode($some_b64_string));
			display_message($arguments->{'type'}, $arguments->{'header'}, $arguments->{'content'});
		} else {
			display_message("error", "Wystąpił błąd!", "Wystąpił błąd. Nie załączono jego opisu.");
		}
	}

	if(isset($_GET['error']))
	{
		parse_error_message($_GET['error']);
	}
?>