<?php
	session_start();

	###############################################
	#               customization				  #
	###############################################

	# database connection
	$db_host = "mysql"; #MySQL host
    $db_username = "esit_db"; #MySQL username
    $db_password = "esit_db"; #MySQL password
    $db_database = "esit_db"; #MySQL db name
    $db_charset = "utf8"; #MySQL charset

	# broker connection
	$rabbit_mq_host = "rabbitmq"; #Broker's host
    $rabbit_mq_port = 5672; #Broker's access port
    $rabbit_mq_user = "esit_user"; #Broker's access username
    $rabbit_mq_password = "123456"; #Broker's access password

	# mailing settings
	$mail_name = "ESIT Mailing Module"; #Name of the sender
	$mail_smtp_debug = 0; #Mail debugging
	$mail_smtp_auth = true;

	# network and APIs
	$worker_network_private_key = "write_something_complicated_here!"; #Change it to provide higher level of data safety during transfer via API

	# default variables
	$default_variables = array(
		'general_title' => 'My First ESIT app',
		'general_motd' => 'Change your MOTD',
		'general_url' => 'localhost',
		'general_timezone' => 'Europe/Warsaw',
		'general_workers_allowed_addr' => '["localhost", "worker", "172.18.0.1"]',
		'plugin_custom_error_broker_url' => 'localhost',
		'plugin_mailing_module_host' => 'localhost',
		'plugin_mailing_module_port' => 587,
		'plugin_mailing_module_username' => 'YourUsername',
		'plugin_mailing_module_password' => 'YourPassword',
		'plugin_mailing_module_protocol' => 'starttls'
	);

	###############################################
	#		   	 database connection	     	  #
	###############################################

	$dsn = "mysql:host=$db_host;dbname=$db_database;options='--client_encoding=$db_charset'";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $db_username, $db_password, $options);


	###############################################
	#              general toolbox				  #
	###############################################

	function get_misc_value($key)
	{
		global $pdo;
		$config_array = array();

		$db_query = $pdo->prepare('SELECT DISTINCT * FROM MISC WHERE misc_name=:key');
		$db_query->execute(['key' => $key]);

		while($row = $db_query->fetch())
		{
			return $row['misc_value'];
		}

		global $default_variables;
		if(isset($default_variables[$key]))
		{
			return $default_variables[$key];
		} else {
			return "";
		}
	}

	function force_to_login()
	{
		header("Location: /login");
		echo('<meta http-equiv="refresh" content="0; url=/login" />');
		die;
	}

	function kick()
	{
		if(boolval(get_misc_value('plugin_errors')) and boolval(get_misc_value('plugin_custom_error_broker_url')))
		{
			$error_link = get_misc_value('plugin_custom_error_broker_url');
		} else {
			if(boolval(get_misc_value('general_url')))
			{
				$error_link = get_misc_value('general_url')."/error.php";
			} else {
				$error_link = "http://localhost/error.php";
			}
		}

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

	function check_session_timeout()
	{
		if(isset($_SESSION['SESSION_TIMEOUT']))
		{
			if($_SESSION['SESSION_TIMEOUT']<strtotime("now"))
			{
				session_destroy();
				force_to_login();
			} else {
				$_SESSION['SESSION_TIMEOUT'] += 18000;
			}
		} else {
			force_to_login();
		}
	}

	function net_check_if_trusted()
	{
		if(in_array($_SERVER['REMOTE_ADDR'],json_decode(get_misc_value('general_workers_allowed_addr'))))
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

	try {
		date_default_timezone_set(get_misc_value('general_timezone'));
	} catch (Exception $e) {
		echo("Unvalid timezone.");
	}

	if(boolval(get_misc_value('plugin_debugging')))
    {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
    }
?>