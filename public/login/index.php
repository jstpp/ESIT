<?php
	include(__DIR__.'/../../include/config/config_init.php');
	if(isset($_SESSION['AUTH_ID']))
	{
		echo('<meta http-equiv="refresh" content="0; url=../app/index.php" />');
		die;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
		<script src="https://kit.fontawesome.com/8a8540bd68.js" crossorigin="anonymous"></script>
		<style> 
			body {
				margin: 0;
				background-color: #3e4145;

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

				background-color: #313136;

				border: 0.2vw solid #2a2c2e;
				border-radius: 1vw;
				transition: 0.2s;
				user-select: none;
				animation: entrance 1s;
				font-size: 1vmax;
			}

			.login_input {
				background-color: #3e4145;
				color: white;
				padding: 1vmax 1vmax;
				font-size: inherit;
				font-family: inherit;
				width: 80%;

				border: 0.2vmax solid #2a2c2e;
				border-radius: 0.5vmax;

				transition: 0.3s;
			}
			.login_input:focus {
				outline: none;
				border: 0.2vmax solid #00b3ff;
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

		</style>
	</head>
	<body>
		<center style="position: fixed; width: 100%; height: 99vh;">
			<div class="window" id="default_login_window">
				<form method="POST" action="process.php?s=auth">
					<h2>Logowanie</h2>
					<br />
					<?php
						if(isset($_GET['response']))
						{
							if ($_GET['response']=="failed")
							{
								echo("<div class='prompt_window'>
									<p>Logowanie nie powiodło się. <br />Spróbuj ponownie!</p>
								</div>
								<br />");
							} else if ($_GET['response']=="logout")
							{
								echo("<div class='prompt_window' style='background-color: #00b3ff; border-left: 0.5vmax solid rgb(4, 103, 145);'>
									<p>Wylogowano pomyślnie,<br /><a href='../' style='color: white; font-weight: bold; text-decoration: dotted;'>wróć na stronę główną</a>!</p>
								</div>
								<br />");
							} else if ($_GET['response']=="registered")
							{
								echo("<div class='prompt_window' style='background-color: #00b3ff; border-left: 0.5vmax solid rgb(4, 103, 145);'>
									<p>Rejestracja powiodła się!<br />Teraz możesz się zalogować.</p>
								</div>
								<br />");
							} else if ($_GET['response']=="passrecoverysuccess")
							{
								echo("<div class='prompt_window' style='background-color: #00b3ff; border-left: 0.5vmax solid rgb(4, 103, 145);'>
									<p>Twoje hasło zostało pomyślnie zmienione - teraz możesz się zalogować.</p>
								</div>
								<br />");
							}
						}
					?>
					
					<input class="login_input" type="text" name="auth_username" placeholder="Nazwa użytkownika" required />
					<br />
					<br />
					<input class="login_input" type="password" name="auth_password" placeholder="Hasło" required />
					<br />
					<?php if($enable_mailing_module==true) echo('<a onClick=\'document.getElementById("password_recovery_window_mail").style.display = "block"; document.getElementById("default_login_window").style.display = "none";\' class="simple_href" style="float: left; margin-top: 0.5vmax; margin-left: 7%; font-size: 1vmax;">Nie pamiętasz hasła?</a>'); ?>
					<br />
					<br />
					<input class="login_submit" type="submit" value="Zaloguj się" />
					<br style="clear: both;"/>
					<br />
				</form>
			</div>
			<div class="window" id="password_recovery_window_mail" style="display: none;">
				<form method="POST" action="process.php?s=passrecovery_mail">
					<h2>Odzyskaj hasło</h2>
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
										<p>Nie udało się wysłać Ci maila. <br />Spróbuj ponownie!</p>
									</div>
									<br />");
								} else if ($_GET['response']=="success")
								{
									echo("<div class='prompt_window' style='background-color: #00b3ff; border-left: 0.5vmax solid rgb(4, 103, 145);'>
										<p>Mail wysłany pomyślnie! Sprawdź swoją skrzynkę pocztową (w tym SPAM).</p>
									</div>
									<br />");
								}
							}
						}
					?>
					
					<input class="login_input" type="text" name="recovery_pass_mail" id="recovery_pass_mail" placeholder="Adres e-mail" required />
					<br />
					<br />
					<br />
					<input class="login_submit" type="submit" value="Wyślij link" />
					<a href="index.php" class="login_submit_gray" style="margin-right: 0.5vmax;">Anuluj</a>
					<br style="clear: both;"/>
					<br />
				</form>
			</div>
			<div class="window" id="password_recovery_window_pass" style="display: none;">
				<form method="POST" action="process.php?s=passrecovery_pass">
					<h2>Odzyskaj hasło</h2>
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
										<p>Brak dostępu.</p>
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
										<p>Coś poszło nie tak. <br />Spróbuj ponownie. </p>
									</div>
									<br />");
								}
							}
						}
					?>
					
					<input class="login_input" type="password" name="recovery_pass_1" id="recovery_pass_1" placeholder="Nowe hasło" required />
					<br />
					<br />
					<input class="login_input" type="password" name="recovery_pass_2" id="recovery_pass_2" placeholder="Powtórz nowe hasło" required />
					<br />
					<br />
					<br />
					<input class="login_submit" type="submit" id="recovery_pass_3" value="Zmień hasło" />
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
	</body>
</html>