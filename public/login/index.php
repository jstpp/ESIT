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
			.prompt_window {
				background-color: #f54242;
				border-left: 0.5vmax solid rgb(177, 32, 32);
				border-radius: 0.5vmax;
				width: 79%;
				padding: 0.2vmax 1vmax;
			}

		</style>
	</head>
	<body>
		<center style="position: fixed; width: 100%; height: 99vh;">
			<div class="window">
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
							}
						}
					?>
					
					<input class="login_input" type="text" name="auth_username" placeholder="Nazwa użytkownika" required />
					<br />
					<br />
					<input class="login_input" type="password" name="auth_password" placeholder="Hasło" required />
					<br />
					<br />
					<br />
					<input class="login_submit" type="submit" value="Zaloguj się" />
					<br style="clear: both;"/>
					<br />
				</form>
			</div>
		</center>
	</body>
</html>