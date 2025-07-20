<style>
	.window {
		width: 95%;
		margin-left: 2.5%;
		margin-top: 1vw;

		background-color: var(--container-bg);;

		border: 0.2vw solid #2a2c2e;
		border-radius: 1vw;
	}

	.window .window_title {
		margin-left: 5%;
		margin-top: 1.5vw;
	}

	.window #results {
		width: 90%;
		margin-left: 5%;
		user-select: none;
	}
	.window #results td {
		border-top: 0.1vw solid gray;
		padding: 0.5vw 0.5vw;
	}
	.window #results td a {
		font-weight: bold;
		text-align: center;
		transition: 0.3s;
		cursor: pointer;
	}
	.window #results tr {
		transition: 0.2s;
		cursor: default;
	}
	.window #results tr:hover {
		background-color: #2a2c2e;
	}

	.set_link {
		padding: 1vw 1vw;
		margin-left: 4%;
		
		display: flex;

		font-weight: bold;
		color: inherit;
		text-decoration: none;
		user-select: none;
		transition: 0.2s;
		cursor: pointer;
	}
	.set_link:hover {
		background-color: #2a2c2e;
	}

	.org_user {
		padding: 1% 1%;
		margin-left: 5%;
		margin-top: 0.5vw;
		width: 88%;

		background-color: var(--container-hover-bg);
		transition: 0.2s;
		cursor: default;
		user-select: none;
	}
	.org_user:hover {
		background-color: var(--bg);
	}
	.org_user table {
		padding: 0.8vw 0.8vw;
		width: 80%;
		float: left;
	}
	.org_user a, .button {
		padding: 1vw 1vw;
		float: right;

		background-color: #00b3ff;
		color: white;
		border-radius: 5px;
		cursor: pointer;
		transition: 0.2s;
		text-decoration: none;
	}
	.button_red {
		padding: 1vw 1vw;
		float: right;

		background-color:rgb(255, 0, 0);
		color: white;
		border-radius: 5px;
		cursor: pointer;
		transition: 0.2s;
		text-decoration: none;
	}
	.button_red:hover {
		background-color:rgb(255, 110, 110);
	}
	.org_user a:hover, .button:hover {
		background-color: #6ed4ff;
	}
	.forminput_2 {
		background: transparent;
		outline: none;
		border: none;
		border-bottom: 0.3vmin solid rgb(204, 204, 204);
		font: inherit;
		width: 98%;
		margin-left: auto;
		margin-right: auto;
		margin-top: 2vmin;
		padding: 1% 1%;
	}
</style>

<center>
	<h1>Panel administracyjny</h1>
</center>
<div id="user_dialog" style="display: none; justify-content: center; align-items: center; margin: 0; min-width: 100vw; min-height: 100vh; background-color: rgba(0,0,0,0.6); position: fixed; top: 0; left: 0; z-index: 999">
	<span onClick="document.getElementById('user_dialog').style.display = 'none';" style="font-size: 4.5vmax; float: right; margin-right: 2vw; cursor: pointer; position: fixed; top: 0; right: 0;">×</span>
	<div style="background-color: #dae2e6; color: black; width: 30vmax; max-height: 80vh; padding: 1vmax 1vmax; border-radius: 0.2vmax;">
		<h2 style="text-align: center;">Dodaj użytkownika</h2>
		<br />
		<form method="POST" id="user_form">
			<input name="username" class="forminput_2" type="text" placeholder="Nazwa użytkownika" />
			<input name="name" class="forminput_2" type="text" placeholder="Imię" />
			<input name="surname" class="forminput_2" type="text" placeholder="Nazwisko" />
			<input name="org" class="forminput_2" type="text" placeholder="Organizacja, ew. klasa" />
			<br />
			<br />
			<input name="mail" class="forminput_2" type="text" placeholder="Adres e-mail" />
			<input name="password" class="forminput_2" type="password" placeholder="Hasło" />
			<input name="priority" class="forminput_2" type="number" min="<?php echo($_SESSION['AUTH_LEVEL']+1); ?>" placeholder="Priorytet" />
		</form>
		<br />
		<br />
		<a class="button" id="button_1" onClick="document.getElementById('user_form').submit();" style="margin-right: 1%; margin-bottom: 1%;"><i class="fa fa-plus"></i>&nbsp;Dodaj użytkownika</a>
		<a class="button" id="button_2" onClick="document.getElementById('user_form').submit();" style="margin-right: 1%; margin-bottom: 1%;"><i class="fa fa-edit"></i>&nbsp;Zapisz zmiany</a>
		<a class="button_red" id="button_3" style="margin-right: 1%; margin-bottom: 1%;"><i class="fa fa-remove"></i>&nbsp;Usuń użytkownika</a>
		<br style="clear: both;"/>
	</div>
</div>
<div class="window">
	<h2 class="window_title">Użytkownicy</h2>
	<p style="margin-left: 5%;">
		<i class='fas fa-info-circle'></i>&nbsp;&nbsp;W tej sekcji zmodyfikujesz uprawnienia użytkowników i nadasz uprawnienia nauczyciela
		<br />
		<a class="button" style="margin-right: 5%;" onClick="add_user();"><i class="fa fa-plus"></i>&nbsp;Dodaj użytkownika</a>

		<br style="clear: both;" />
		<?php 
			$db_query = $pdo->prepare('SELECT * FROM USERS WHERE role=:xrole');
			$db_query->execute(['xrole' => $_SESSION['AUTH_LEVEL']]);

			while($row = $db_query->fetch())
			{
				echo('<div class="org_user">
			<table style="border-spacing: 3vmin;">
				<tr id="user_'.$row['USER_ID'].'">
					<td><b><span id="org" style="display: none; font-size: 0.1vmin;">'.$row['organization'].'</span><span id="name">'.$row['name'].'</span> <span id="surname">'.$row['surname'].'</span> (<span id="username">'.$row['username'].'</span>)</b></td>
					<td style="text-align: right;"><b style="padding: 1vmin 1.3vmin; border-radius: 0.2vmin; color: white; background-color: rgb(97, 0, 153);"><span id="priority">'.$row['role'].'</span></b></td>
					<td><span id="mail">'.$row['mail'].'</span></td>
					<td style="text-align: right;">Ostatnie logowanie: <b>'.$row['lastlogin'].'</b></td>
				</tr>
			</table>
			<br style="clear: both;"/>
		</div>');
			}
			$db_query = $pdo->prepare('SELECT * FROM USERS WHERE role>:xrole ORDER BY role');
			$db_query->execute(['xrole' => $_SESSION['AUTH_LEVEL']]);

			while($row = $db_query->fetch())
			{
				echo('<div class="org_user">
			<table style="border-spacing: 3vmin;">
				<tr id="user_'.$row['USER_ID'].'">
					<td><b><span id="org" style="display: none; font-size: 0.1vmin;">'.$row['organization'].'</span><span id="name">'.$row['name'].'</span> <span id="surname">'.$row['surname'].'</span> (<span id="username">'.$row['username'].'</span>)</b></td>
					<td style="text-align: right;"><b style="padding: 1vmin 1.3vmin; border-radius: 0.2vmin; color: white; background-color: rgb(0, 117, 153);"><span id="priority">'.$row['role'].'</span></b></td>
					<td><span id="mail">'.$row['mail'].'</span></td>
					<td style="text-align: right;">Ostatnie logowanie: <b>'.$row['lastlogin'].'</b></td>
				</tr>
			</table>
			<a onClick="modify_user(\''.$row['USER_ID'].'\');"><i class="fa fa-edit"></i>&nbsp;Modyfikuj</a>
			<br style="clear: both;"/>
		</div>');
			}
		?>
	</p>
	<br />
	<script>
		function modify_user(userid)
		{
			document.querySelector('#user_dialog h2').innerHTML = document.querySelector('#user_'+userid+' #username').innerHTML;
			document.querySelector('#user_dialog input[name="username"]').value = document.querySelector('#user_'+userid+' #username').innerHTML;
			document.querySelector('#user_dialog input[name="name"]').value = document.querySelector('#user_'+userid+' #name').innerHTML;
			document.querySelector('#user_dialog input[name="surname"]').value = document.querySelector('#user_'+userid+' #surname').innerHTML;
			document.querySelector('#user_dialog input[name="org"]').value = document.querySelector('#user_'+userid+' #org').innerHTML;
			document.querySelector('#user_dialog input[name="mail"]').value = document.querySelector('#user_'+userid+' #mail').innerHTML;
			document.querySelector('#user_dialog input[name="priority"]').value = document.querySelector('#user_'+userid+' #priority').innerHTML;
			document.querySelector('#user_dialog #button_3').href = 'process.php?r=remove_user&uid='+userid;

			document.querySelector('#user_dialog input[name="password"]').style.display = 'none';

			document.querySelector('#user_dialog #button_1').style.display = 'none';
			document.querySelector('#user_dialog #button_2').style.display = 'block';
			document.querySelector('#user_dialog #button_3').style.display = 'block';

			document.getElementById('user_dialog').style.display = 'flex';
			document.getElementById('user_form').action = 'process.php?r=modify_user&uid='+userid;
		}
		function add_user()
		{
			document.querySelector('#user_dialog h2').innerHTML = "Nowy użytkownik";
			document.querySelector('#user_dialog input[name="username"]').value = "";
			document.querySelector('#user_dialog input[name="name"]').value = "";
			document.querySelector('#user_dialog input[name="surname"]').value = "";
			document.querySelector('#user_dialog input[name="mail"]').value = "";
			document.querySelector('#user_dialog input[name="priority"]').value = "";
			document.querySelector('#user_dialog input[name="org"]').value = "";

			document.querySelector('#user_dialog input[name="password"]').style.display = 'block';

			document.querySelector('#user_dialog #button_1').style.display = 'block';
			document.querySelector('#user_dialog #button_2').style.display = 'none';
			document.querySelector('#user_dialog #button_3').style.display = 'none';

			document.getElementById('user_dialog').style.display = 'flex';
			document.getElementById('user_form').action = 'process.php?r=create_user';
		}
	</script>
</div>
<br />
<br />