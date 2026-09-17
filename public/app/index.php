<?php
	include(__DIR__.'/../../include/app/core.php');

	if(!is_logged_in()) force_to_login();
	check_session_timeout();

	$db_query = $pdo->prepare('SELECT * FROM USERS WHERE USER_ID=:uid LIMIT 1');
	$db_query->execute(['uid' => $_SESSION['AUTH_ID']]);

	$user = $db_query->fetch();
	$settings = $user ? json_decode($user['settings']) : null;

	$db_query = $pdo->prepare('SELECT misc_value FROM MISC WHERE misc_name LIKE "general_title" LIMIT 1');
    $db_query->execute();
    $general_title = $db_query->fetchColumn() ?: "ESIT";

	$allowed_pages = [
		'dashboard'     => ['permission' => 'main.display.dashboard', 'path' => 'dashboard.php', 'menu_id' => 'dashboard'],
		'admin'         => ['permission' => 'main.display.admin.configuration', 'path' => 'admin.php', 'menu_id' => 'admin'],
		'channels'   	=> ['permission' => 'main.display.channels', 'path' => 'channels.php', 'menu_id' => 'channels'],
		'mysolutions'   => ['permission' => 'main.display.mysolutions', 'path' => 'mysolutions.php', 'menu_id' => 'mysolutions'],
		'myexamsadmin'  => ['permission' => 'main.display.channels.myexamsadmin', 'path' => 'myexamsadmin.php', 'menu_id' => 'myexamsadmin'],
		'settings'      => ['permission' => 'main.display.user_settings', 'path' => 'settings.php', 'menu_id' => 'settings'],
		'portal'        => ['permission' => 'main.display.portal.settings', 'path' => 'portal.php', 'menu_id' => 'portal'],
		'diagnostics'   => ['permission' => 'main.display.admin.diagnostics', 'path' => 'diagnostics.php', 'menu_id' => 'diagnostics'],
		'logs'    		=> ['permission' => 'main.display.admin.logs', 'path' => 'logs.php', 'menu_id' => 'logs'],
		'algresult'     => ['permission' => 'main.display.algresult.user', 'path' => 'results/algresult.php'],
		'testresult'    => ['permission' => 'main.display.testresult.user', 'path' => 'results/testresult.php'],
		'ctfresult'     => ['permission' => 'main.display.ctfresult.user', 'path' => 'results/ctfresult.php'],
		'formresult'    => ['permission' => 'main.display.formresult.user', 'path' => 'results/formresult.php'],
		'problem'       => ['permission' => 'main.display.problem', 'path' => 'problem.php', 'required' => ['id']],
		'channel'       => ['permission' => 'main.display.channel', 'path' => 'channel.php'],
		'addpost'       => ['permission' => 'main.display.portal.addpost', 'path' => 'portal/addpost.php'],
		'modifypost'    => ['permission' => 'main.display.portal.modifypost', 'path' => 'portal/modifypost.php'],
		'addproblem'    => ['permission' => 'main.display.channels.addproblem', 'path' => 'add_problem.php'],
		'check_the_form'=> ['permission' => 'main.display.channels.check_the_form', 'path' => 'check_the_form.php'],
	];

	$current_p = $_GET['p'] ?? 'dashboard';
	if (!array_key_exists($current_p, $allowed_pages)) {
		$current_p = 'dashboard';
	}
	$page_config = $allowed_pages[$current_p];

	if (!has_permission($page_config['permission'])) kick();

	if (isset($script_config['required'])) {
        foreach ($script_config['required'] as $req) {
            if (!isset($_GET[$req]) && !isset($_POST[$req])) kick();
        }
    }


?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo(__("App")); ?> | <?php  echo(htmlspecialchars($general_title)); ?></title>
		<link href="/include/fonts/Montserrat/Montserrat.css" rel="stylesheet">
		<link href="/include/fonts/fontawesome/css/all.min.css" rel="stylesheet">
		<link rel="icon" href="/img/favicon.ico" type="image/x-icon">
		<style> 
			html {
				scroll-behavior: smooth;
			}
			
			:root {
				--bg: rgba(39, 55, 71, 1);
				--container-bg: rgba(32, 43, 54, 1);
				--container-hover-bg: rgba(21, 33, 46, 1);
				--container-hover-bg-textbox: rgba(6, 20, 34, 1);
				--text: #dae2e6;
				--horizontal-menu-bg: rgba(21, 33, 46, 1);
				--vertical-menu-bg: rgba(32, 43, 54, 1);
				--notifications-menu-bg: rgba(32, 43, 54, 0.8);
			}

			[data-theme="light"] {
				--bg: #dae2e6;
				--container-bg: #c8c8c8;
				--container-hover-bg:rgb(170, 170, 170);
				--container-hover-bg-textbox: #dae2e6;
				--text: #3e4145;
				--horizontal-menu-bg: #a5a5a5;
				--vertical-menu-bg: #c8c8c8;
				--notifications-menu-bg: rgba(200,200,200,0.8);
			}

			[data-theme="dark"] {
				--bg: rgba(39, 55, 71, 1);
				--container-bg: rgba(32, 43, 54, 1);
				--container-hover-bg: rgba(21, 33, 46, 1);
				--container-hover-bg-textbox: rgba(6, 20, 34, 1);
				--horizontal-menu-bg: rgba(21, 33, 46, 1);
				--vertical-menu-bg: rgba(32, 43, 54, 1);
				--notifications-menu-bg: rgba(32, 43, 54, 0.8);
			}

			body {
				margin: 0;
				background-color: var(--bg);

				font-size: 1vw;
				font-family: 'Montserrat';
				color: var(--text);
			}
			
			#page_content {
				width: 82vw;
				margin-top: 3vw;
				float: right;
				-webkit-animation: entrance 1s;
				animation: entrance 1s;
			}

			@keyframes entrance {
				0% {
					opacity: 0;
				}
				100% {
					opacity: 1;
				}
			}

			.window {
				width: calc(95% - 0.5vmax);
				margin-left: 2%;
				margin-right: 2%;
				margin-top: 1vw;
				padding: 0.5vmax;

				background-color: var(--container-bg);

				box-shadow: 0 0 0.1vmax 0.2vmax var(--container-hover-bg);
				border-radius: 1vw;
			}

			.window .window_title {
				margin-left: 5%;
				margin-top: 1.5vw;
			}

			.window a {
				text-decoration: none;
				color: rgb(0, 179, 255);
			}
			.button {
				padding: 1vw 1vw;
				float: right;
				text-decoration: none;
				margin-left: 0.5vw;

				background-color: #00b3ff;
				color: white !important;
				border-radius: 5px;
				cursor: pointer;
				transition: 0.2s;
			}
			.window .button:hover {
				background-color:rgb(0, 121, 173);
			}

		</style>
		<?php if (isset($settings) && $settings->dark_mode == 0): ?>
			<script>document.documentElement.setAttribute('data-theme', 'light');</script>
		<?php endif; ?>
	</head>
	<body>
		<?php
			include(__DIR__.'/../../include/app/menus/horizontal.php');
			include(__DIR__.'/../../include/app/menus/vertical.php');
			include(__DIR__.'/../../include/app/menus/notifications.php');
		?>
		<div id="page_content">
			<?php
				include(__DIR__.'/../../include/app/profiles/profile.php');
				include(__DIR__.'/../../include/app/'.$page_config['path']);
			?>
			<?php if (isset($page_config['menu_id'])): ?>
				<script>
					const activeMenu = document.getElementById(<?= json_encode($page_config['menu_id']) ?>);
					if (activeMenu) {
						activeMenu.style.background = 'var(--container-hover-bg)'; 
						activeMenu.style.color = '#00b3ff';
					}
				</script>
			<?php endif; ?>
		</div>
	</body>
</html>