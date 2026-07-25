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
		'general_url' => 'http://localhost',
		'general_timezone' => 'Europe/Warsaw',
		'general_workers_allowed_addr' => '["localhost", "worker", "127.0.0.1", "::1", "172.18.0.1"]',
		'general_trusted_proxies' => '["127.0.0.1", "::1"]',
		'plugin_custom_error_broker_url' => 'http://localhost',
		'plugin_mailing_module_host' => 'localhost',
		'plugin_mailing_module_port' => 587,
		'plugin_mailing_module_username' => 'YourUsername',
		'plugin_mailing_module_password' => 'YourPassword',
		'plugin_mailing_module_protocol' => 'starttls'
	);

	###############################################
	#		   	 database connection	     	  #
	###############################################

	$dsn = "mysql:host=$db_host;dbname=$db_database;charset=$db_charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

	try {
    	$pdo = new PDO($dsn, $db_username, $db_password, $options);
	}
	catch (Throwable $t) {
		echo("Database initialization failure. Please reload.");
		die;
	}


	###############################################
	#              general toolbox				  #
	###############################################

	function get_misc_value($key): string
	{
		global $pdo;
		$config_array = array();

		$db_query = $pdo->prepare('SELECT * FROM MISC WHERE misc_name=:key LIMIT 1');
		$db_query->execute(['key' => $key]);

		if($row = $db_query->fetch())
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

	function force_to_login(): void
	{
		header("Location: /login", true, 302);
		echo('<meta http-equiv="refresh" content="0; url=/login" />');
		die;
	}

	function kick(): void
	{
		if(boolval(get_misc_value('plugin_errors')) && boolval(get_misc_value('plugin_custom_error_broker_url')))
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

		redirect($error_link);
	}

	function redirect($dest): void
	{
		if (!headers_sent()) {
			header("Location: ".$dest, true, 302);
			die;
		} else {
			echo '<meta http-equiv="refresh" content="0; url='.htmlspecialchars($dest).'"/>';
			die;
		}
	}

	function display_message($type, $header, $message): void
	{
		if(!isset($type) || !isset($header) || !isset($message)) kick();
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

	function is_an_user($key): bool
	{
		global $pdo;

		$db_query = $pdo->prepare('SELECT COUNT(*) AS count FROM USERS WHERE username=:key1 OR mail=:key2');
		$db_query->execute(['key1' => $key, 'key2' => $key]);
		
		return ($db_query->fetch()['count'] > 0);
	}

	function is_logged_in(): bool
	{
		return isset($_SESSION['AUTH_ID']);
	}

	function is_admin(): bool
	{
		return isset($_SESSION['AUTH_LEVEL']) && $_SESSION['AUTH_LEVEL']<=3;
	}

	function has_a_priority($n): bool
	{
		return isset($_SESSION['AUTH_LEVEL']) && $_SESSION['AUTH_LEVEL']<=$n;
	}

	function check_session_timeout(): void
	{
		if(isset($_SESSION['SESSION_TIMEOUT']))
		{
			if($_SESSION['SESSION_TIMEOUT']<strtotime("now"))
			{
				session_destroy();
				force_to_login();
				die;
			} else {
				$_SESSION['SESSION_TIMEOUT'] = time() + 14400;
			}
		} else {
			force_to_login();
		}
	}

	function net_check_if_trusted(): bool
	{
		$allowed = json_decode(get_misc_value('general_workers_allowed_addr'), true);
        return is_array($allowed) && in_array(get_real_client_ip(), $allowed, true);
	}

	function get_real_client_ip(): string
	{
		$remote_addr = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

		if (!in_array($remote_addr, json_decode(get_misc_value('general_trusted_proxies'), true), true)) {
			return $remote_addr;
		}
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$client_ip = trim($ips[0]);

			if (filter_var($client_ip, FILTER_VALIDATE_IP)) {
				return $client_ip;
			}
		}

		return $remote_addr;
	}

	function include_plugins_for($element, $plugin = null): bool
	{
		if(isset($element))
		{
			global $pdo;
			$element = basename($element);
			if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $element)) {
				return False; 
			}
			$base_dir = realpath(__DIR__ . "/../plugins");

			if(!isset($plugin)) {
				$db_query = $pdo->prepare('SELECT * FROM MISC WHERE misc_value=1 AND misc_name LIKE "community_plugin_%"');
				$db_query->execute();

				while($row = $db_query->fetch())
				{
					$plugin_name = substr($row['misc_name'], 17);
            
					if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $plugin_name)) {
						continue;
					}

					$plugin_path = realpath($base_dir . "/" . $plugin_name . "/include/" . $element . ".php");

					try {
						if ($plugin_path && strpos($plugin_path, $base_dir) === 0 && file_exists($plugin_path)) {
							include($plugin_path);
						}
					} catch (Throwable $e) {
						continue;
					}
				}
			} else {
				try {
					if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $plugin)) return False;

					$plugin_path = realpath($base_dir . "/" . $plugin . "/include/" . $element . ".php");
					
					if ($plugin_path && strpos($plugin_path, $base_dir) === 0 && file_exists($plugin_path)) {
						include($plugin_path);
					} else {
						return False;
					}
				} catch (Throwable $e) {
					return False;
				}
			}
			return True;
		} else {
			return False;
		}
	}

	function copy_directory($src, $dst): bool 
	{
		$dir = opendir($src);
		try {
			mkdir($dst, 0777, true);
		} catch (Throwable $e) {
			closedir($dir);
			return False;
		}

		try {
			while (false !== ($file = readdir($dir))) {
				if (($file != '.') && ($file != '..')) {
					if (is_dir($src . '/' . $file)) {
						copy_directory($src . '/' . $file, $dst . '/' . $file);
					} else {
						copy($src . '/' . $file, $dst . '/' . $file);
					}
				}
			}
			closedir($dir);
		} catch (Throwable $e) {
			closedir($dir);
			return False;
		}
		return True;
	}

	function delete_directory($dir): bool
	{
		if (!is_dir($dir)) return False;

		$files = array_diff(scandir($dir), array('.', '..'));

		foreach ($files as $file) {
			$path = "$dir/$file";
			(is_dir($path)) ? delete_directory($path) : unlink($path);
		}

		return rmdir($dir);
	}

	function load_img_input($img = null, $target_root_dir = null): string
	{
		$allowed_types = [
							'image/jpeg' => 'jpg',
							'image/png'  => 'png',
							'image/webp' => 'webp'
						 ];

		$placeholder = "../img/placeholder.jpeg";

		if(!isset($img) || !isset($target_root_dir) || !isset($img['tmp_name']) || $img['error'] !== UPLOAD_ERR_OK) {
			echo "Sorry, something went wrong. (1)";
			return $placeholder;
		}

		$base_dir = realpath($target_root_dir);
		if (!$base_dir || !is_dir($base_dir)) {
			echo "Sorry, something went wrong. (2)";
			return $placeholder;
		}

		if(isset($img) && isset($target_root_dir))
		{

			try {
				$finfo = new finfo(FILEINFO_MIME_TYPE);
				$mime_type = $finfo->file($img["tmp_name"]);
				$check = getimagesize($img["tmp_name"]);

				if($check == false) {
					#echo "File is not an image. (3)";
					return $placeholder;
				}

				if ($img["size"] > 1000000) {
					#echo "Image is too large! (4)";
					return $placeholder;
				}

				if (!array_key_exists($mime_type, $allowed_types)) {
					#echo "Only JPG, JPEG, PNG and WEBP are allowed. (5)";
					return $placeholder;
				}

				$index = hash('sha256', (new DateTime())->format('Uv') . random_bytes(5));
				$target_file = $base_dir.DIRECTORY_SEPARATOR.$index.".".$allowed_types[$mime_type];

				if (file_exists($target_file)) {
					#echo "Sorry, file already exists. (6)";
					return $placeholder;
				}

				if (strpos($target_file, $base_dir) !== 0) {
					#echo "Sorry, something went wrong. (7)";
					return $placeholder;
				}

				if (!move_uploaded_file($img["tmp_name"], $target_file)) {
					#echo "<br/>Sorry, an error occurred. (8)";
					return $placeholder;
				}
			} catch (Throwable $e) {
				#echo "<br/>Sorry, an error occurred. (9)";
				return $placeholder;
			}
			return $target_root_dir.DIRECTORY_SEPARATOR.$index.".".$allowed_types[$mime_type];
		} else {
			#echo "<br/>Sorry, an error occurred. (10)";
			return $placeholder;
		}
	}

	function parse_flash_messages(): void
	{
		if(isset($_SESSION['flash_messages']) && count($_SESSION['flash_messages'])>0)
		{
			foreach($_SESSION['flash_messages'] as $message)
			{
				display_message($message->{'type'}, $message->{'header'}, $message->{'content'});
			}
			unset($_SESSION['flash_messages']);
		}
	}

	function insert_flash_message($type, $header, $content = null): void
	{
		if (!isset($_SESSION['flash_messages']) || !is_array($_SESSION['flash_messages'])) {
			$_SESSION['flash_messages'] = [];
		}

		$_SESSION['flash_messages'][] = [
			'type'    => $type,
			'header'  => $header,
			'content' => $content ?? '',
		];
	}

	function problem_type_identification($type): array {
		$problem_types = [
			'alg'     => ['full_name' => 'Algorytmiczne', 'icon' => 'fas fa-file-code', 'color' => 'rgba(0, 121, 250, 1)'],
			'ctf'     => ['full_name' => 'Capture The Flag', 'icon' => 'fa-solid fa-flag', 'color' => 'rgba(208, 72, 72, 1)'],
			'och'     => ['full_name' => 'Pytania jednokrotnego wyboru', 'icon' => 'fa fa-check-square-o', 'color' => 'rgba(14, 149, 109, 1)'],
			'mch'     => ['full_name' => 'Pytania wielokrotnego wyboru', 'icon' => 'fa fa-check-square', 'color' => 'rgba(218, 130, 6, 1)'],
			'opn'     => ['full_name' => 'Zadanie otwarte', 'icon' => 'fa fa-pencil-square-o', 'color' => 'rgba(69, 47, 165, 1)']
		];

		return $problem_types[$type] ?? [];
	}

	function check_problemset_availability($set_id, $pdo): array
	{
		$status = array('is_available' => 'false', 'condition_title' => 'Unknown');
		$db_query = $pdo->prepare('SELECT * FROM PROBLEMSETS WHERE SET_ID = :sid');
		$db_query->execute(['sid' => $set_id]);
		$set = $db_query->fetch();

		if (!$set)
		{ 
			$status['is_available'] = False;
			return $status;
		}
		$condition = json_decode($set['depends_on']);
		$db_query = $pdo->prepare('SELECT * FROM PROBLEMSETS WHERE SET_ID = :sid');
		$db_query->execute(['sid' => $condition->id]);
		$status['condition_title'] = $db_query->fetch()['title'];

		if (!isset($condition->type) || $condition->type === 'none') 
		{
			$status['is_available'] = True;
			return $status;
		}
		$required_set_id = $condition->id;

		$db_query = $pdo->prepare('SELECT PROBLEM_ID, maxpoints FROM PROBLEMS WHERE problemset = :pid');
		$db_query->execute(['pid' => $required_set_id]);
		$problems = $db_query->fetchAll();

		if (empty($problems)) 
		{
			$status['is_available'] = False;
			return $status;
		}

		foreach ($problems as $row) {
			$cdb_query = $pdo->prepare('SELECT MAX(score) AS maxscore FROM SUBMISSIONS WHERE problem_id = :pid AND user_id = :uid');
			$cdb_query->execute([
				'pid' => $row['PROBLEM_ID'], 
				'uid' => $_SESSION['AUTH_ID']
			]);
			$ctx = $cdb_query->fetch();

			$maxscore = ($ctx && (int)$ctx['maxscore'] !== -1) ? (float)$ctx['maxscore'] : 0;
			$maxpoints = (float)$row['maxpoints'];

			$percentage = ($maxpoints > 0) ? ($maxscore / $maxpoints) * 100 : 0;

			if (round($percentage, 0) < 50) {
				$status['is_available'] = False;
				return $status;
			}
		}
		$status['is_available'] = True;
		return $status;
	}



	###############################################
	#       just another extension point		  #
	###############################################

	include_plugins_for("core");


	###############################################
	#              some automation				  #
	###############################################

	if(isset($_SESSION['flash_messages'])) parse_flash_messages();

	try {
		date_default_timezone_set(get_misc_value('general_timezone'));
	} catch (Exception $e) {
		echo("Invalid timezone.");
	}

	include(__DIR__."/../diagnostics/error_handler.php");


	if(boolval(get_misc_value('plugin_debugging')))
    {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
    } else {
		ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');
        error_reporting(0);
	}
?>