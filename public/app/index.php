<?php
	include(__DIR__.'/../../include/app/core.php');

	if(!is_logged_in()) force_to_login(); #functions from core
	check_session_timeout();

	$db_query = $pdo->prepare('SELECT * FROM USERS WHERE USER_ID=:uid LIMIT 1');
	$db_query->execute(['uid' => $_SESSION['AUTH_ID']]);

	$user = $db_query->fetch();
	$settings = $user ? json_decode($user['settings']) : null;

	$db_query = $pdo->prepare('SELECT misc_value FROM MISC WHERE misc_name LIKE "general_title" LIMIT 1');
    $db_query->execute();
    $general_title = $db_query->fetchColumn() ?: "ESIT";

	$allowed_pages = [
		'dashboard'     => ['priority' => 0, 'path' => 'dashboard.php', 'menu_id' => 'dashboard'],
		'admin'         => ['priority' => 3, 'path' => 'admin.php', 'menu_id' => 'admin'],
		'sets'          => ['priority' => 0, 'path' => 'sets.php', 'menu_id' => 'sets'],
		'archive'       => ['priority' => 0, 'path' => 'archive.php', 'menu_id' => 'archive'],
		'mysolutions'   => ['priority' => 0, 'path' => 'mysolutions.php', 'menu_id' => 'mysolutions'],
		'myexamsadmin'  => ['priority' => 4, 'path' => 'myexamsadmin.php', 'menu_id' => 'myexamsadmin'],
		'settings'      => ['priority' => 0, 'path' => 'settings.php', 'menu_id' => 'settings'],
		'portal'        => ['priority' => 3, 'path' => 'portal.php', 'menu_id' => 'portal'],
		'diagnostics'   => ['priority' => 3, 'path' => 'diagnostics.php', 'menu_id' => 'diagnostics'],
		'algresult'     => ['priority' => 0, 'path' => 'results/algresult.php'],
		'testresult'    => ['priority' => 0, 'path' => 'results/testresult.php'],
		'ctfresult'     => ['priority' => 0, 'path' => 'results/ctfresult.php'],
		'formresult'    => ['priority' => 0, 'path' => 'results/formresult.php'],
		'quest'         => ['priority' => 0, 'path' => 'quest.php'],
		'set'           => ['priority' => 0, 'path' => 'set.php'],
		'addpost'       => ['priority' => 3, 'path' => 'portal/addpost.php'],
		'modifypost'    => ['priority' => 3, 'path' => 'portal/modifypost.php'],
		'addproblem'    => ['priority' => 3, 'path' => 'add_problem.php'],
		'check_the_form'=> ['priority' => 3, 'path' => 'check_the_form.php'],
	];

	$current_p = $_GET['p'] ?? 'dashboard';
	if (!array_key_exists($current_p, $allowed_pages)) {
		$current_p = 'dashboard';
	}
	$page_config = $allowed_pages[$current_p];

	if ($page_config['priority'] > 0 && !has_a_priority($page_config['priority'])) {
		kick();
	}


?>
<!DOCTYPE html>
<html>
	<head>
		<title>Aplikacja | <?php  echo(htmlspecialchars($general_title)); ?></title>
		<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
		<link rel="icon" href="../img/favicon.ico" type="image/x-icon">
		<script src="https://kit.fontawesome.com/8a8540bd68.js" crossorigin="anonymous"></script>
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
		</style>
		<?php if (isset($settings) && $settings->dark_mode === "0"): ?>
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