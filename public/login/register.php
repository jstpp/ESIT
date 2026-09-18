<?php
	include(__DIR__.'/../../include/app/core.php');
	if(isset($_SESSION['AUTH_ID'])) redirect("../app/index.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<link href="/include/fonts/Montserrat/Montserrat.css" rel="stylesheet">
		<link href="/include/fonts/fontawesome/css/all.min.css" rel="stylesheet">
		<link rel="icon" href="/img/favicon.ico" type="image/x-icon">
		<style> 
			:root {
				--bg: rgba(39, 55, 71, 1);
				--container-bg: rgba(32, 43, 54, 1);
				--container-hover-bg: rgba(21, 33, 46, 1);
				--container-hover-bg-textbox: rgba(6, 20, 34, 1);
				--text: rgba(218, 226, 230, 1);
				--horizontal-menu-bg: rgba(21, 33, 46, 1);
				--vertical-menu-bg: rgba(32, 43, 54, 1);
				--notifications-menu-bg: rgba(32, 43, 54, 0.8);
				--highlight-color: rgba(0, 179, 255, 1);
			}
			
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
				width: 33vw;
				height: fit-content;
				padding: 1vw;
				margin-top: 4vw;
				margin-bottom: 3vw;

				background-color: rgba(32, 43, 54, 1);

				box-shadow: 0 0 0.1vw 0.2vw rgba(21, 33, 46, 1);
				border-radius: 1vw;
				transition: 0.2s;
				user-select: none;
				animation: entrance 1s;
				font-size: 1vw;
			}

			.login_input {
				background-color: rgba(39, 55, 71, 1);
				color: white;
				padding: 1vw 1vw;
				font-size: inherit;
				font-family: inherit;
				width: 80%;

				border: 0.2vw solid var(--container-hover-bg);
				border-radius: 0.5vw;

				transition: 0.3s;
			}
			.login_input:focus {
				outline: none;
				box-shadow: 0 0 0.1vw 0.2vw #00b3ff;
			}

			.login_submit {
				background-color: #00b3ff;
				color: white;
				padding: 1vw 1vw;
				float: right;
				margin-right: 2vw;
				font-family: inherit;
				font-size: inherit;
				border: none;
				border-radius: 0.5vw;
				cursor: pointer;

				transition: 0.3s;
			}
			.login_submit:hover {
				background-color:rgb(47, 120, 151);
			}
			.login_submit_gray {
				background-color: #808080ff;
				color: white;
				padding: 1vw 1vw;
				float: right;
				font-family: inherit;
				font-size: inherit;
				border: none;
				border-radius: 0.5vw;
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

			.data-invalid {
				color: rgb(253, 75, 75);
				margin-left: 1vmax;
			}
			.data-invalid:before {
				position: relative;
				left: -1vmax;
				content: "✖";
			}
			.data-valid {
				color: rgb(70, 231, 65);
				margin-left: 1vmax;
			}
			.data-valid:before {
				position: relative;
				left: -1vmax;
				content: "✔";
			}

			.registration_input
			{
				border: 0.15vmax solid rgb(210, 210, 210);
				border-radius: 0.5vmax;
				color: rgb(255, 255, 255);
				padding: 0;
				transition: 0.3s;
			}
			.registration_input:hover
			{
				border: 0.15vmax solid #00b3ff;
				color: #00b3ff;
			}
			.registration_input input
			{
				width: 99%;
				margin: 0;
				background: transparent;
				color: inherit;
				padding: 0.8vmax 0.8vmax;
				font-size: inherit;
				border: 0;
				outline: 0;
			}

			#r_i_checkbox {
				padding: 0.8vmax 0.8vmax;
				border: 0.15vmax solid rgb(210, 210, 210);
				border-radius: 0.5vmax;
			}

			#r_i_checkbox legend{
				color: rgb(170, 170, 170);
			}

			.registration_button
			{
				margin-top: 4.8vw;
				border: 0;
				padding: 1vmax 1vmax;
				width: 32.7vw;
				font: inherit;
				border-radius: 0.5vmax;
				background-color:#00b3ff;
				color: white;
				transition: 0.3s;
				cursor: pointer;
			}

			.registration_button:hover
			{
				border: 0;
				padding: 1vmax 1vmax;
				font: inherit;
				border-radius: 0.5vmax;
				background-color:rgb(0, 78, 146);
				color: white;
			}

		</style>
	</head>
	<body>
		<div style="display: flex; gap: 2vmax; width: 100%; height: 99vh; justify-content: center;">
			<div style="width: 35vw;">
				<form method="POST" action="/app/process.php?r=register">
					<div class="window">
						<fieldset class="registration_input" id="r_i_name" >
							<legend>&nbsp;<?php echo(__('Name')); ?>:&nbsp;</legend>
							<input type="text" name="name" onChange="validate_data();" />
						</fieldset>
						<fieldset class="registration_input" id="r_i_surname" >
							<legend>&nbsp;<?php echo(__('Surname')); ?>:&nbsp;</legend>
							<input type="text" name="surname" onChange="validate_data();" />
						</fieldset>
						<fieldset class="registration_input" id="r_i_org" >
							<legend>&nbsp;<?php echo(__('Organization')); ?>:&nbsp;</legend>
							<input type="text" name="org" onChange="validate_data();" />
						</fieldset>
						<fieldset class="registration_input" id="r_i_username" style="margin-top: 4vmax;">
							<legend>&nbsp;<?php echo(__('Username')); ?>:&nbsp;</legend>
							<input type="text" name="username" onChange="validate_data();" />
						</fieldset>
						<fieldset class="registration_input" id="r_i_mail" style="margin-top: 4vmax;">
							<legend>&nbsp;<?php echo(__('E-mail address')); ?>:&nbsp;</legend>
							<input type="text" name="mail" onChange="validate_data();" />
						</fieldset>
						<fieldset class="registration_input" id="r_i_pass" >
							<legend>&nbsp;<?php echo(__('Password')); ?>:&nbsp;</legend>
							<input type="password" name="pass" onChange="validate_data();"/>
						</fieldset>
						<fieldset class="registration_input" id="r_i_pass_repeat" >
							<legend>&nbsp;<?php echo(__('Repeat password')); ?>:&nbsp;</legend>
							<input type="password" name="pass_repeat" onChange="validate_data();" />
						</fieldset>
						<input type="submit" value="<?php echo(__('Register')); ?>" class="registration_button" id="r_i_registration_button" style="display: none;"/>
					</div>
				</form>
				<br />
				<div style="color: rgba(39, 55, 71, 1);">.</div>
			</div>
			<div style="width: 35vw;">
				<div style="position: fixed;">
					<div class="window"> 
						<h1 style="text-align: center;"><?php echo(__("Register")); ?></h1>
						<fieldset id="r_i_validation" style="margin-top: 2vmax; padding: 1.5vmax; border-radius: 0.5vw;">
							<legend><?php echo(__('Input validation')); ?></legend>
							<b><?php echo(__('Your username')); ?>...</b>
							<p class="data-invalid" id="c_1"><?php echo(__('...must be at least 6 characters long')); ?></p>
							<p class="data-invalid" id="c_2"><?php echo(__('...must be unique')); ?></p>
							<p class="data-invalid" id="c_10"><?php echo(__('...cannot contain special characters other than _ or -')); ?></p>

							<b><?php echo(__('Your password')); ?>...</b>
							<p class="data-invalid" id="c_3"><?php echo(__('...must be at least 8 characters long')); ?></p>
							<p class="data-invalid" id="c_4"><?php echo(__('...must contain at least 1 special character')); ?></p>
							<p class="data-invalid" id="c_5"><?php echo(__('...must contain at least 1 uppercase letter')); ?></p>
							<p class="data-invalid" id="c_6"><?php echo(__('...must contain at least 1 digit')); ?></p>
							<p class="data-invalid" id="c_7"><?php echo(__('...must match the password entered in the second field')); ?></p>

							<b><?php echo(__('Your email address')); ?>...</b>
							<p class="data-invalid" id="c_11"><?php echo(__('...must have a valid format')); ?></p>
							<p class="data-invalid" id="c_8"><?php echo(__('...must be unique')); ?></p>

							<b><?php echo(__('Name of the organization you belong to')); ?>...</b>
							<p class="data-invalid" id="c_9"><?php echo(__('...must be provided by you')); ?></p>
							<script>
								let e = 0;
								function check_data()
								{
									if(e>10)
									{
										document.getElementById('r_i_registration_button').style.display = 'block';
									} else {
										document.getElementById('r_i_registration_button').style.display = 'none';
									}
								}

								function isValidEmail(email) {
									const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
									return emailRegex.test(email);
								}

								function validate_data()
								{
									e = 0;
									if(document.querySelector('input[name="username"]').value.length>5)
									{
										e++;
										document.getElementById('c_1').classList.add("data-valid");
										document.getElementById('c_1').classList.remove("data-invalid");

										const xhttp = new XMLHttpRequest();
										xhttp.onload = function() {
											if(this.responseText=="OK")
											{
												e++;
												document.getElementById('c_2').classList.add("data-valid");
												document.getElementById('c_2').classList.remove("data-invalid");
											} else {
												document.getElementById('c_2').classList.add("data-invalid");
												document.getElementById('c_2').classList.remove("data-valid");
											}
											check_data();
										}
										xhttp.open("GET", "/app/process.php?r=registration_is_unique&value=" + document.querySelector('input[name="username"]').value);
										xhttp.send();
									} else {
										document.getElementById('c_1').classList.add("data-invalid");
										document.getElementById('c_1').classList.remove("data-valid");
									}

									if(document.querySelector('input[name="pass"]').value.length>7)
									{
										e++;
										document.getElementById('c_3').classList.add("data-valid");
										document.getElementById('c_3').classList.remove("data-invalid");
									} else {
										document.getElementById('c_3').classList.add("data-invalid");
										document.getElementById('c_3').classList.remove("data-valid");
									}

									var specialchars = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
									if(specialchars.test(document.querySelector('input[name="pass"]').value))
									{
										e++;
										document.getElementById('c_4').classList.add("data-valid");
										document.getElementById('c_4').classList.remove("data-invalid");
									} else {
										document.getElementById('c_4').classList.add("data-invalid");
										document.getElementById('c_4').classList.remove("data-valid");
									}

									var capitalchars = /[A-Z]/g;
									if(capitalchars.test(document.querySelector('input[name="pass"]').value))
									{
										e++;
										document.getElementById('c_5').classList.add("data-valid");
										document.getElementById('c_5').classList.remove("data-invalid");
									} else {
										document.getElementById('c_5').classList.add("data-invalid");
										document.getElementById('c_5').classList.remove("data-valid");
									}

									var numberchars = /[0-9]/g;
									if(numberchars.test(document.querySelector('input[name="pass"]').value))
									{
										e++;
										document.getElementById('c_6').classList.add("data-valid");
										document.getElementById('c_6').classList.remove("data-invalid");
									} else {
										document.getElementById('c_6').classList.add("data-invalid");
										document.getElementById('c_6').classList.remove("data-valid");
									}

									if(document.querySelector('input[name="pass"]').value==document.querySelector('input[name="pass_repeat"]').value && document.querySelector('input[name="pass"]').value.length>7)
									{
										e++;
										document.getElementById('c_7').classList.add("data-valid");
										document.getElementById('c_7').classList.remove("data-invalid");
									} else {
										document.getElementById('c_7').classList.add("data-invalid");
										document.getElementById('c_7').classList.remove("data-valid");
									}

									if(document.querySelector('input[name="mail"]').value.length>3)
									{
										const xhttp = new XMLHttpRequest();
										xhttp.onload = function() {
											if(this.responseText=="OK")
											{
												e++;
												document.getElementById('c_8').classList.add("data-valid");
												document.getElementById('c_8').classList.remove("data-invalid");
											} else {
												document.getElementById('c_8').classList.add("data-invalid");
												document.getElementById('c_8').classList.remove("data-valid");
											}
											check_data();
										}
										xhttp.open("GET", "/app/process.php?r=registration_is_unique&value=" + document.querySelector('input[name="mail"]').value);
										xhttp.send();
									} else {
										document.getElementById('c_8').classList.add("data-invalid");
										document.getElementById('c_8').classList.remove("data-valid");
									}

									if(document.querySelector('input[name="name"]').value.length>1 && document.querySelector('input[name="surname"]').value.length>1 && document.querySelector('input[name="org"]').value.length>3)
									{
										e++;
										document.getElementById('c_9').classList.add("data-valid");
										document.getElementById('c_9').classList.remove("data-invalid");
									} else {
										document.getElementById('c_9').classList.add("data-invalid");
										document.getElementById('c_9').classList.remove("data-valid");
									}

									var specialchars = /[ `!@#$%^&*()+=\[\]{};':"\\|,.<>\/?~]/;
									if(specialchars.test(document.querySelector('input[name="username"]').value))
									{
										document.getElementById('c_10').classList.add("data-invalid");
										document.getElementById('c_10').classList.remove("data-valid");
									} else {
										e++;
										document.getElementById('c_10').classList.add("data-valid");
										document.getElementById('c_10').classList.remove("data-invalid");
									}

									if(isValidEmail(document.querySelector('input[name="mail"]').value))
									{
										document.getElementById('c_11').classList.add("data-valid");
										document.getElementById('c_11').classList.remove("data-invalid");
										e++;
									} else {
										document.getElementById('c_11').classList.add("data-invalid");
										document.getElementById('c_11').classList.remove("data-valid");
									}

									check_data();
								}
							</script>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
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