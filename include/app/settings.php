<style>
	.window {
		width: 95%;
		margin-left: 2.5%;
		margin-top: 1vw;

		background-color: var(--container-bg);

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
		padding: 1vw 1vw;
		margin-left: 5%;
		margin-top: 0.5vw;
		width: 87%;

		background-color: #2a2c2e;
		transition: 0.2s;
		cursor: default;
		user-select: none;
	}
	.org_user:hover {
		background-color: #3e4145;
	}
	.org_user table {
		padding: 0.8vw 0.8vw;
		width: 70%;
		float: left;
	}
	.org_user a {
		padding: 1vw 1vw;
		float: right;

		background-color: #00b3ff;
		color: white;
		border-radius: 5px;
		cursor: pointer;
		transition: 0.2s;
	}
	.org_user a:hover {
		background-color: #6ed4ff;
	}
	.window .forminput, .window .forminput_a {
		border: 0; 
		padding: 1vw 1.5vw; 
		color: #dae2e6; 
		background-color: #2a2c2e; 
		font-family: inherit;
		transition: 0.4s;
		outline: none;
	}
	.window .forminput_a:hover {
		background-color: #3e4145;
		cursor: pointer;
	}
</style>

<center>
	<h1>Ustawienia konta</h1>
</center>
<div class="window">
	<h2 class="window_title">Podstawowe informacje</h2>
	<p style="margin-left: 5%;">
		<i class="fa fa-address-book"></i>&nbsp;&nbsp;<b>Użytkownik:&emsp;&nbsp;<?php echo($user['username']." (".$user['name']." ".$user['surname'].")"); ?></b><br />
		<i class="fa fa-bank"></i>&nbsp;&nbsp;<b>Organizacja:</b>&emsp;&nbsp;<?php echo($user['organization']); ?><br />
		<i class="fa fa-envelope"></i>&nbsp;&nbsp;<b>Adres e-mail:</b>&emsp;<?php echo($user['mail']); ?><br />
	</p>
	<br />
</div>
<div class="window">
	<h2 class="window_title">Bezpieczeństwo</h2>
	<h3 style="margin-left: 5%;">Zmiana hasła</h3>
	<form id="change_password_form" method="POST" action="process.php?r=change_password" style="margin-left: 5%;">
		<input type="password" class="forminput" name="password_old" placeholder="Stare hasło" required/>
		<input type="password" class="forminput" name="password_new1" placeholder="Nowe hasło" required/>
		<input type="password" class="forminput" name="password_new2" placeholder="Powtórz nowe hasło" required/>
		<a class="forminput_a" style="margin-left: 5%;" onClick="document.getElementById('change_password_form').submit();">Zmień hasło</a>
	</form>
	<br />
	<br />
</div>
<div class="window">
	<h2 class="window_title">Wygląd</h2>
	<form id="appearance_form" method="POST" action="process.php?r=settings_appearance" style="margin-left: 5%;">
		<label for="app_dark_theme">Tryb ciemny:&emsp;</label>
		<select id="app_dark_theme" name="app_dark_theme" class="forminput">
			<option value="1" <?php if(!isset($settings->{'dark_mode'}) or isset($settings->{'dark_mode'}) and $settings->{'dark_mode'}=="1") { echo('selected'); } ?>>Włączony</option>
			<option value="0" <?php if(isset($settings->{'dark_mode'}) and $settings->{'dark_mode'}=="0") { echo('selected'); } ?>>Wyłączony</option>
		</select>
		<br />
		<br />
		<label for="ace_theme">Motyw edytora kodu:&emsp;</label>
		<select id="ace_theme" name="ace_theme" class="forminput" onChange='editor.setTheme("ace/theme/"+document.getElementById("ace_theme").value.replace(".css",""));'>
			<?php
				$themes = scandir('modules/ace/css/theme');

				for($i = 2; $i<count($themes); $i++)
				{
					if(isset($settings->{'code_editor_theme'}) and $settings->{'code_editor_theme'}==$themes[$i])
					{
						echo('<option value="'.$themes[$i].'" selected>'.str_replace(array('_','.css'), ' ', $themes[$i]).'</option>');
					} else {
						if(!isset($settings->{'code_editor_theme'}) and $themes[$i]=="dracula.css")
						{
							echo('<option value="'.$themes[$i].'" selected>'.str_replace(array('_','.css'), ' ', $themes[$i]).'</option>');
						} else {
							echo('<option value="'.$themes[$i].'">'.str_replace(array('_','.css'), ' ', $themes[$i]).'</option>');
						}
					}
				}
			?>
		</select>
		<br />
		<br />
		<div id="example_code_editor" style="width: 30%;">
			<textarea id="editor_code" name="example_code_editor" style="display: none;" readonly>
			</textarea>
			<div id="editor" style="position: relative; height: 5vmin; width: 100%; border-radius: 2vmin;"></div>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/ace.js" type="text/javascript" charset="utf-8"></script>
			<script>
				var editor = ace.edit("editor");
				editor.setValue('int main() { std::cout<<"Hi!"; }', 1);
				editor.setTheme("ace/theme/"+document.getElementById('ace_theme').value.replace('.css',''));
				editor.session.setMode("ace/mode/c_cpp");
				editor.setReadOnly(true);
			</script>
		</div>
	</form>
	<br />
	<br />
	<a class="forminput_a" style="margin-left: 5%;" onClick="document.getElementById('appearance_form').submit();">Zapisz ustawienia wyglądu</a>
	<br />
	<br />
	<br />
</div>
<br />
<br />