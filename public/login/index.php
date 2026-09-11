<?php
	include(__DIR__.'/../../include/app/core.php');
	if(isset($_SESSION['AUTH_ID']))
	{
		echo('<meta http-equiv="refresh" content="0; url=../app/index.php" />');
		die;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link href="/include/fonts/Montserrat/Montserrat.css" rel="stylesheet">
		<script src="https://kit.fontawesome.com/8a8540bd68.js" crossorigin="anonymous"></script>
		<link rel="icon" href="/img/favicon.ico" type="image/x-icon">
		<style> 
			body {
				margin: 0;
				background-color: rgba(39, 55, 71, 1);

				font-size: 1vw;
				font-family: 'Montserrat';
				color: #dae2e6;
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
				width: 30vmax;
				margin: 0;
				position: absolute;
				top: 45%;
				left: 50%;
				-ms-transform: translate(-50%, -50%);
				transform: translate(-50%, -50%);

				background-color: rgba(32, 43, 54, 1);

				box-shadow: 0 0 0.1vmax 0.2vmax rgba(21, 33, 46, 1);
				border-radius: 1vw;
				transition: 0.2s;
				user-select: none;
				animation: entrance 1s;
				font-size: 1vmax;
			}

			.login_input {
				background-color: rgba(39, 55, 71, 1);
				color: white;
				padding: 1vmax 1vmax;
				font-size: inherit;
				font-family: inherit;
				width: 80%;

				border: 0.2vmax solid var(--container-hover-bg);
				border-radius: 0.5vmax;

				transition: 0.3s;
			}
			.login_input:focus {
				outline: none;
				box-shadow: 0 0 0.1vmax 0.2vmax #00b3ff;
			}

			.login_submit {
				background-color: #00b3ff;
				color: white;
				padding: 1vmax 1vmax;
				float: right;
				margin-right: 2vmax;
				font-family: inherit;
				font-size: inherit;
				border: none;
				border-radius: 0.5vmax;
				cursor: pointer;

				transition: 0.3s;
			}
			.login_submit:hover {
				background-color:rgb(47, 120, 151);
			}
			.login_submit_gray {
				background-color: #808080ff;
				color: white;
				padding: 1vmax 1vmax;
				float: right;
				font-family: inherit;
				font-size: inherit;
				border: none;
				border-radius: 0.5vmax;
				cursor: pointer;
				text-decoration: none;

				transition: 0.3s;
			}
			.login_submit_gray:hover {
				background-color:rgba(74, 74, 74, 1);
			}
			.prompt_window {
				background-color: #f54242;
				border-left: 0.5vmax solid rgb(177, 32, 32);
				border-radius: 0.5vmax;
				width: 79%;
				padding: 0.2vmax 1vmax;
			}
			.simple_href
			{
				text-decoration: none;
				font-family: inherit;
				color: #00b3ff;
				transition: 0.3s;
				cursor: pointer;
			}
			.simple_href:hover
			{
				color:rgb(47, 120, 151);
			}

			.i18n_select {
				height: 2vmax;
				width: fit-content;
				padding: 0.25vmax;
				background-color: var(--container-hover-bg);
				border-radius: 0.5vmax;
				display: flex;
				flex-direction: row;
				gap: 0.25vmax;
				cursor: pointer;
				user-select: none;

				position: fixed;
				bottom: 1vmax;
				left: 1vmax;
			}

			.i18n_select_flag {
				background-repeat: no-repeat;
				background-size: cover;
				width: 2vmax;
				border-radius: 0.5vmax;
				display: none;
			}

			.i18n_select_active {
				display: flex;
			}

		</style>
	</head>
	<body>
		<center style="position: fixed; width: 100%; height: 99vh;">
			<div class="window" id="default_login_window">
				<form method="POST" action="process.php?s=auth">
					<h2><?php echo(__("Log in")); ?></h2>
					<br />
					<?php
						if(isset($_GET['response']))
						{
							if ($_GET['response']=="failed")
							{
								echo("<div class='prompt_window'>
									<p>".__("Login unsuccessful").". <br />".__("Try again")."!</p>
								</div>
								<br />");
							} else if ($_GET['response']=="logout")
							{
								echo("<div class='prompt_window' style='background-color: #00b3ff; border-left: 0.5vmax solid rgb(4, 103, 145);'>
									<p>".__("Logged out successfully").",<br /><a href='../' style='color: white; font-weight: bold; text-decoration: dotted;'>".__("Return to the main site")."</a>!</p>
								</div>
								<br />");
							} else if ($_GET['response']=="registered")
							{
								echo("<div class='prompt_window' style='background-color: #00b3ff; border-left: 0.5vmax solid rgb(4, 103, 145);'>
									<p>".__("Registration successful")."!<br />".__("You can log in now").".</p>
								</div>
								<br />");
							} else if ($_GET['response']=="passrecoverysuccess")
							{
								echo("<div class='prompt_window' style='background-color: #00b3ff; border-left: 0.5vmax solid rgb(4, 103, 145);'>
									<p>".__("Your password has been changed successfully")." - ".__("You can log in now").".</p>
								</div>
								<br />");
							}
						}
					?>
					
					<input class="login_input" type="text" name="auth_username" placeholder="<?php echo(__("Username")); ?>" required />
					<br />
					<br />
					<input class="login_input" type="password" name="auth_password" placeholder="<?php echo(__("Password")); ?>" required />
					<br />
					<?php if(boolval(get_misc_value('plugin_mailing'))) echo('<a onClick=\'document.getElementById("password_recovery_window_mail").style.display = "block"; document.getElementById("default_login_window").style.display = "none";\' class="simple_href" style="float: left; margin-top: 0.5vmax; margin-left: 7%; font-size: 1vmax;">'.__("Forgot your password?").'</a>'); ?>
					<br />
					<input class="login_submit" type="submit" value="<?php echo(__("Log in")); ?>" />
					<br style="clear: both;"/>
				</form>
				<p><?php echo(__("Don't have an account yet?")); ?> <a class="simple_href" href="../rejestracja.php"><?php echo(__("Create an account")); ?></a>.</p>
			</div>
			<div class="window" id="password_recovery_window_mail" style="display: none;">
				<form method="POST" action="process.php?s=passrecovery_mail">
					<h2><?php echo(__("Password recovery")); ?></h2>
					<br />
					<?php
						if(isset($_GET['passrecoverymail']))
						{
							echo('<script>document.getElementById("password_recovery_window_mail").style.display = "block";</script>');
							echo('<script>document.getElementById("default_login_window").style.display = "none";</script>');
							if(isset($_GET['response']))
							{
								if ($_GET['response']=="fail")
								{
									echo("<div class='prompt_window'>
										<p>".__("We couldn't send You recovery mail").". <br />".__("Try again")."!</p>
									</div>
									<br />");
								} else if ($_GET['response']=="success")
								{
									echo("<div class='prompt_window' style='background-color: #00b3ff; border-left: 0.5vmax solid rgb(4, 103, 145);'>
										<p>".__("Email sent successfully! Please check your email (including SPAM).")."</p>
									</div>
									<br />");
								}
							}
						}
					?>
					
					<input class="login_input" type="text" name="recovery_pass_mail" id="recovery_pass_mail" placeholder="<?php echo(__("E-mail address")); ?>" required />
					<br />
					<br />
					<br />
					<input class="login_submit" type="submit" value="<?php echo(__("Send link")); ?>" />
					<a href="index.php" class="login_submit_gray" style="margin-right: 0.5vmax;"><?php echo(__("Cancel")); ?></a>
					<br style="clear: both;"/>
					<br />
				</form>
			</div>
			<div class="window" id="password_recovery_window_pass" style="display: none;">
				<form method="POST" action="process.php?s=passrecovery_pass">
					<h2><?php echo(__("Password recovery")); ?></h2>
					<br />
					<?php
						$ok = 1;
						if(isset($_GET['passrecoverypass']))
						{
							echo('<script>document.getElementById("password_recovery_window_pass").style.display = "block";</script>');
							echo('<script>document.getElementById("default_login_window").style.display = "none";</script>');

							if(isset($_GET['mail']) and isset($_GET['token']))
							{
								$_SESSION['pr_token'] = $_GET['token'];
								$_SESSION['pr_mail'] = filter_var($_GET['mail'], FILTER_SANITIZE_EMAIL);
							} else {
								if(!isset($_GET['response'])) 
								{
									echo("<div class='prompt_window'>
										<p>".__("Access denied").".</p>
									</div>
									<br />");
									$ok = 0;
								}
							}
							
							if(isset($_GET['response']))
							{
								if ($_GET['response']=="fail")
								{
									echo("<div class='prompt_window'>
										<p>".__("Something went wrong").". <br />".__("Try again").". </p>
									</div>
									<br />");
								}
							}
						}
					?>
					
					<input class="login_input" type="password" name="recovery_pass_1" id="recovery_pass_1" placeholder="<?php echo(__("New password")); ?>" required />
					<br />
					<br />
					<input class="login_input" type="password" name="recovery_pass_2" id="recovery_pass_2" placeholder="<?php echo(__("Repeat new password")); ?>" required />
					<br />
					<br />
					<br />
					<input class="login_submit" type="submit" id="recovery_pass_3" value="<?php echo(__("Change password")); ?>" />
					<?php
						if($ok==0)
						{
							echo("<script>document.getElementById('recovery_pass_1').style.display = 'none'; document.getElementById('recovery_pass_2').style.display = 'none'; document.getElementById('recovery_pass_3').style.display = 'none'</script>");
						}
					?>
					<br style="clear: both;"/>
					<br />
				</form>
			</div>
		</center>
		<div class="i18n_select">
			<div id="en_US.UTF-8" class="i18n_select_flag" style="background-image: url('../img/i18n/flags/us.svg');" onClick="i18n_select_choose(this);">&emsp;</div>
			<div id="pl_PL.UTF-8" class="i18n_select_flag" style="background-image: url('../img/i18n/flags/pl.svg');" onClick="i18n_select_choose(this);">&emsp;</div>
		</div>

		<script>
			var i18n_select_mode = 0;
			const flags = document.querySelectorAll(".i18n_select_flag")
			document.getElementById('<?php echo($_SESSION['lang']); ?>').style.display = 'flex';
			function i18n_select_choose(x) {
				if (i18n_select_mode === 1) {
					i18n_select_mode = 0;

					const urlParams = new URLSearchParams(window.location.search);
					urlParams.set('lang', x.id);
					window.location.search = urlParams.toString();
				} else {
					for(let i = 0; i < flags.length; i++){
						flags[i].style.display = 'flex';
					}
					i18n_select_mode = 1;
				}
			}
		</script>
	</body>
</html>