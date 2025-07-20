<?php
	include(__DIR__.'/../../include/config/config_init.php');
	include(__DIR__.'/../../include/app/core.php');

	if(!is_logged_in()) force_to_login(); #functions from core

	$db_query = $pdo->prepare('SELECT DISTINCT * FROM USERS WHERE USER_ID=:uid');
	$db_query->execute(['uid' => $_SESSION['AUTH_ID']]);

	$user = $db_query->fetch();
	$settings = json_decode($user['settings']);

	$db_query = $pdo->prepare('SELECT * FROM MISC WHERE misc_name LIKE "general_%"');
    $db_query->execute();
    while($row = $db_query->fetch())
    {
        if($row['misc_name']=="general_title") $general_title = $row['misc_value'];
    }
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Aplikacja | <?php if(isset($general_title)) { echo($general_title); } else { echo("ESIT"); } ?></title>
		<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
		<link rel="icon" href="../img/favicon.ico" type="image/x-icon">
		<script src="https://kit.fontawesome.com/8a8540bd68.js" crossorigin="anonymous"></script>
		<style> 
			:root {
				--bg: #3e4145;
				--container-bg: #313136;
				--container-hover-bg: #2a2c2e;
				--text: #dae2e6;
			}

			[data-theme="light"] {
				--bg: #dae2e6;
				--container-bg: #c8c8c8;
				--container-hover-bg:rgb(170, 170, 170);
				--text: #3e4145;
			}

			[data-theme="dark"] {
				--bg: #3e4145;
				--container-bg: #313136;
				--container-hover-bg: #2a2c2e;
				--text: #dae2e6;
			}

			body {
				margin: 0;
				background-color: var(--bg);

				font-size: 1vw;
				font-family: 'Montserrat';
				color: var(--text);
			}
			
			#page_content {
				width: 80vw;
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
	</head>
	<body>
		<?php
			if(isset($settings))
			{
				if($settings->{'dark_mode'}=="0") echo("<script>document.body.setAttribute('data-theme', 'light');</script>");
			}
			include(__DIR__.'/../../include/app/menus/horizontal.php');
			include(__DIR__.'/../../include/app/menus/vertical.php');
			include(__DIR__.'/../../include/app//menus/noftications.php');
		?>
		<div id="page_content">
			<?php
				if (isset($_GET['p']))
				{
					if($_GET['p']=="admin")
					{
						if(!is_logged_in()) force_to_login();
						if(!has_a_priority(3)) kick();
						include(__DIR__.'/../../include/app/admin.php');
						echo("<script>document.getElementById('admin').style.background = '#2a2c2e'; document.getElementById('admin').style.color = '#00b3ff';</script>");
					} else if ($_GET['p']=="sets") {
						if(!is_logged_in()) force_to_login();
						include(__DIR__.'/../../include/app/sets.php');
						echo("<script>document.getElementById('sets').style.background = '#2a2c2e'; document.getElementById('sets').style.color = '#00b3ff';</script>");
					} else if ($_GET['p']=="archive") {
						if(!is_logged_in()) force_to_login();
						include(__DIR__.'/../../include/app/archive.php');
						echo("<script>document.getElementById('archive').style.background = '#2a2c2e'; document.getElementById('archive').style.color = '#00b3ff';</script>");
					} else if ($_GET['p']=="mysolutions") {
						if(!is_logged_in()) force_to_login();
						include(__DIR__.'/../../include/app/mysolutions.php');
						echo("<script>document.getElementById('mysolutions').style.background = '#2a2c2e'; document.getElementById('mysolutions').style.color = '#00b3ff';</script>");
					} else if ($_GET['p']=="myexamsadmin") {
						if(!is_logged_in()) force_to_login();
						if(!has_a_priority(4)) kick();
						include(__DIR__.'/../../include/app/myexamsadmin.php');
						echo("<script>document.getElementById('myexamsadmin').style.background = '#2a2c2e'; document.getElementById('myexamsadmin').style.color = '#00b3ff';</script>");
					} else if ($_GET['p']=="settings") {
						if(!is_logged_in()) force_to_login();
						include(__DIR__.'/../../include/app/settings.php');
						echo("<script>document.getElementById('settings').style.background = '#2a2c2e'; document.getElementById('settings').style.color = '#00b3ff';</script>");
					} else if ($_GET['p']=="dashboard") {
						if(!is_logged_in()) force_to_login();
						include(__DIR__.'/../../include/app/dashboard.php');
						echo("<script>document.getElementById('dashboard').style.background = '#2a2c2e'; document.getElementById('dashboard').style.color = '#00b3ff';</script>");
					} else if ($_GET['p']=="portal") {
						if(!is_logged_in()) force_to_login();
						if(!has_a_priority(3)) kick();
						include(__DIR__.'/../../include/app/portal.php');
						echo("<script>document.getElementById('portal').style.background = '#2a2c2e'; document.getElementById('portal').style.color = '#00b3ff';</script>");
					} else if ($_GET['p']=="algresult") {
						if(!is_logged_in()) force_to_login();
						include(__DIR__.'/../../include/app/results/algresult.php');
					} else if ($_GET['p']=="testresult") {
						if(!is_logged_in()) force_to_login();
						include(__DIR__.'/../../include/app/results/testresult.php');
					} else if ($_GET['p']=="ctfresult") {
						if(!is_logged_in()) force_to_login();
						include(__DIR__.'/../../include/app/results/ctfresult.php');
					} else if ($_GET['p']=="formresult") {
						if(!is_logged_in()) force_to_login();
						include(__DIR__.'/../../include/app/results/formresult.php');
					} else if ($_GET['p']=="quest") {
						if(!is_logged_in()) force_to_login();
						include(__DIR__.'/../../include/app/quest.php');
					} else if ($_GET['p']=="set") {
						if(!is_logged_in()) force_to_login();
						include(__DIR__.'/../../include/app/set.php');
					} else if ($_GET['p']=="addpost") {
						if(!is_logged_in()) force_to_login();
						if(!has_a_priority(3)) kick();
						include(__DIR__.'/../../include/app/portal/addpost.php');
					} else if ($_GET['p']=="modifypost") {
						if(!is_logged_in()) force_to_login();
						if(!has_a_priority(3)) kick();
						include(__DIR__.'/../../include/app/portal/modifypost.php');
					} else if ($_GET['p']=="addproblem") {
						if(!is_logged_in()) force_to_login();
						if(!has_a_priority(3)) kick();
						include(__DIR__.'/../../include/app/add_problem.php');
					} else if ($_GET['p']=="check_the_form") {
						if(!is_logged_in()) force_to_login();
						if(!has_a_priority(3)) kick();
						include(__DIR__.'/../../include/app/check_the_form.php');
					} else {
						include(__DIR__.'/../../include/app/dashboard.php');
						echo("<script>document.getElementById('dashboard').style.background = '#2a2c2e'; document.getElementById('dashboard').style.color = '#00b3ff';</script>");
					}
				} else {
					include(__DIR__.'/../../include/app/dashboard.php');
					echo("<script>document.getElementById('dashboard').style.background = '#2a2c2e'; document.getElementById('dashboard').style.color = '#00b3ff';</script>");
				}
			?>
		</div>
	</body>
</html>