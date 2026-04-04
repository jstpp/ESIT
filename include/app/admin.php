<style>
	.window {
		width: 95%;
		margin-left: 2.5%;
		margin-top: 1vw;

		background-color: var(--container-bg);

		border: 0.2vw solid var(--container-hover-bg);
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
		border-bottom: 0.3vmin solid var(--text);
		color: var(--text);
		font: inherit;
		width: 98%;
		margin-left: auto;
		margin-right: auto;
		margin-top: 2vmin;
		padding: 1% 1%;
	}

	.dark-box {
		background-color: var(--container-hover-bg);
		padding: 1vmax;
		width: auto;
		margin-top: 0.5vmax;
		margin-left: 5%;
		margin-right: 5%;
	}
	.dark-box input[type="text"], .dark-box input[type="password"]
	{
		background-color: var(--container-hover-bg-textbox);
		outline: none;
		border: none;
		color: var(--text);
		font: inherit;
		padding: 0.5vmax;
		margin-top: 0.5vmax;
		width: 40%;
		margin-right: 5%;
	}

	.tag {
		border-radius: 1vmax;
		background-color: var(--container-hover-bg-textbox);
		padding: 0.3vmax 1vmax;
		cursor: default;
	}

	.tag span {
		border-radius: 100%;
		transition: 0.3s;
		cursor: pointer;
		padding: 0 0.3vmax;
	}
	.tag span:hover {
		background-color: var(--bg);
	}

	.switch-checkbox {
		position: relative;
		display: inline-block;
		width: 2vmax;
		height: 1.1vmax;
	}
	.switch-checkbox input{
		width: 0;
		opacity: 0;
		height: 0;
	}
	.switch-checkbox-toggle {
		background-color: #ccc;
		position: absolute;
  		cursor: pointer;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		transition: 0.3s;
		border-radius: 1.1vmax;
	}
	.switch-checkbox-toggle:before {
		position: absolute;
		content: "";
		height: 0.9vmax;
		width: 0.9vmax;
		margin: 0.15vmax;
		background-color: white;
		transition: 0.3s;
		border-radius: 100%;
	}
	.switch-checkbox input:checked + .switch-checkbox-toggle {
		background-color: #00b3ff;
	}
	.switch-checkbox input:checked + .switch-checkbox-toggle:before {
		transform: translateX(0.9vmax);
	}

	.remove_plugin {
		float: right;
		margin: 0.5vmax;
		transition: 0.3s;
		cursor: pointer;
		font-size: 1.5vmax;
		text-decoration: none;
		color: var(--text);
	}
	.remove_plugin:hover {
		color: red;
	}

	#settings_menu {
		position: sticky;
		top: 0;
		z-index: 10;
		margin-left: 2.5%;
		width: 60%;
		display: flex;
		gap: 1vmax;
		align-items: center;
	}
	#settings_menu a {
		color: #00b3ff; 
		text-decoration: none;
		transition: 0.3s;
	}
	#settings_menu a:hover {
		color: var(--text); 
	}

	#search_icon {
		transition: 0.3s;
		cursor: pointer;
	}
	#search_icon:hover {
		color: #00b3ff;
	}

	.github_search_results_item {
		background-color: var(--container-hover-bg);
		border-radius: 0.5vmax;
		margin-bottom: 0.5vmax;
		padding: 1vmax;
		display: flex;
		align-items: center;
		gap: 1vmax;
		text-align: justify;
	}

	.github_search_results_item img {
		height: 3vmax;
		border-radius: 50%;
		margin-left: 1vmax;
		margin-right: 1vmax;
	}

	.download_button {
		font-size: 1.5vmax;
		transition: 0.3s;
		cursor: pointer;
		margin-right: 1vmax;
		text-decoration: none;
		color: white;
	}
	.download_button:hover {
		color: #00b3ff;
	}
</style>

<center>
	<h1>Panel administracyjny</h1>
</center>
<div id="user_dialog" style="display: none; justify-content: center; align-items: center; margin: 0; min-width: 100vw; min-height: 100vh; background-color: rgba(0,0,0,0.6); position: fixed; top: 0; left: 0; z-index: 999">
	<span onClick="document.getElementById('user_dialog').style.display = 'none';" style="font-size: 4.5vmax; float: right; margin-right: 2vw; cursor: pointer; position: fixed; top: 0; right: 0;">×</span>
	<div style="background-color: var(--bg); color: var(--text); width: 30vmax; max-height: 80vh; padding: 1vmax 1vmax; border-radius: 0.2vmax;">
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
<div id="plugins_dialog" style="display: none; justify-content: center; align-items: center; margin: 0; min-width: 100vw; min-height: 100vh; background-color: rgba(0,0,0,0.6); position: fixed; top: 0; left: 0; z-index: 999;">
	<span onClick="document.getElementById('plugins_dialog').style.display = 'none';" style="font-size: 4.5vmax; float: right; margin-right: 2vw; cursor: pointer; position: fixed; top: 0; right: 0;">×</span>
	<div style="background-color: var(--bg); color: var(--text); width: 80vw; height: 80vh; padding: 1vmax 1vmax; border-radius: 0.2vmax; overflow: auto;">
		<h1 style="text-align: center;">Wyszukaj wtyczkę na GitHubie&nbsp;&nbsp;<i class="fa fa-github"></i></h1>
		<center>
			<small>
				Wyszukiwarka wtyczek wyświetla repozytoria oznaczone tagiem <code>esit-plugins</code>
			</small>
		</center>
		<br />
		<div style="width: 90%; margin-left: 5%; background-color: var(--container-hover-bg-textbox); overflow: hidden; border-radius: 0.4vmax; display: flex; align-items: center; justify-content: right;">
			<input type="text" id="github_searchbox" placeholder="Nazwa repozytorium lub słowo kluczowe" style="outline: none; width: 100%; background-color: transparent; color: var(--text); border: none; padding: 1vmax; font-size: 1.2vmax; font-family: inherit; float: left;"/>
			<i class="fa fa-search" id="search_icon" onClick="githubSearch();" style="font-size: 1.2vmax; margin-right: 1.2vmax;"></i>
		</div>
		<div id="github_search_results" style="width: 90%; margin-left: 5%; display: none;">
			<br />
			<h3>Repozytoria użytkownika</h3>
			<div class="github_search_results_item">
				<img src="#" />
				<p style="width: 80%;">
					<b>Repo 1</b> by <b>jdoe</b><br />
					Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
				</p>
				<a class="download_button"><i class="fa fa-download"></i></a>
			</div>		
			<h3>Wyszukiwanie repozytoriów</h3>
		</div>
		<script>
			async function githubSearch() {
				const query = document.getElementById("github_searchbox").value;
    			const resultsDiv = document.getElementById("github_search_results");
				resultsDiv.innerHTML = '';

				const repo_search = await fetch("https://api.github.com/search/repositories?q=" + encodeURIComponent(document.getElementById("github_searchbox").value) + "+topic:esit-plugins&sort=stars&order=desc");
				const repo_result = await repo_search.json();

				if(repo_result.items && repo_result.items.length > 0) {
					const repoHeader = document.createElement('h3');
					repoHeader.textContent = "Popularne repozytoria";
					resultsDiv.appendChild(repoHeader);

					repo_result.items.forEach(repo => {
						const repoDiv = document.createElement('div');
						repoDiv.className = 'github_search_results_item';

						repoDiv.innerHTML = `
							<img src="${repo.owner.avatar_url}" />
							<p style="width: 80%;">
								<b>${repo.name}</b> by <b>${repo.owner.login}</b><br/>
								${repo.description || 'Brak opisu'}
							</p>
							<a href="${repo.html_url}" target="_blank" class="download_button"><i class="fa fa-external-link"></i></a>
							<a href="process.php?r=download_plugin&repo=` + repo.full_name + `&branch=` + repo.default_branch + `" class="download_button"><i class="fa fa-download"></i></a>
						`;
						resultsDiv.appendChild(repoDiv);
					});
				} else {
					const notfoundDiv = document.createElement('center');
					notfoundDiv.innerHTML = "<br /><br />Nie znaleziono wyników."
					resultsDiv.appendChild(notfoundDiv);
				}
				document.getElementById("github_search_results").style.display = 'block';
			}
		</script>
		<br style="clear: both;"/>
	</div>
</div>
<div id="settings_menu">
	<p>Nawigacja:</p>
	<a href="#initial_parameters">Podstawowe parametry</a>
	<a href="#plugins">Zarządzanie wtyczkami</a>
	<a href="#users">Zarządzanie użytkownikami</a>
</div>
<div class="window" id="initial_parameters">
	<h2 class="window_title">Podstawowe parametry</h2>
	<form method="POST" action="process.php?r=modify_config&category=general">
		<?php
			if(boolval(get_misc_value('plugin_portal'))) 
			{
				echo('<i class=\'fas fa-info-circle\' style="margin-left: 5%;"></i>&nbsp;&nbsp;Moduł "Portal" jest włączony: nazwę i MOTD zmienisz w zakładce <a href="?p=portal" style="color: #00b3ff; text-decoration: none;">Zarządzanie portalem</a>.<br /><br />'); 
			} else {
				echo('<div class="dark-box">
				<label for="general_title">Nazwa strony:&emsp;</label>
				<input type="text" id="general_title" name="general_title" placeholder="Nazwa strony" value="'.get_misc_value('general_title').'">
			</div>
			<div class="dark-box">
				<label for="general_motd">Message Of The Day (MOTD strony):&emsp;</label>
				<input type="text" id="general_motd" name="general_motd" placeholder="MOTD strony" value="'.get_misc_value('general_motd').'">
			</div>');
			}
		?>
		<div class="dark-box">
			<label for="general_url">Domyślny adres URL:&emsp;</label>
			<input type="text" id="general_url" name="general_url" placeholder="Domyślny adres URL" value="<?php echo(get_misc_value('general_url')); ?>">
			<br /><small>Adres przez który łączysz się z aplikacją to "https://<b><?php echo($_SERVER['HTTP_HOST']); ?></b>".</small>
		</div>
		<div class="dark-box">
			<label for="general_timezone">Domyślna strefa czasowa:&emsp;</label>
			<input type="text" id="general_timezone" name="general_timezone" placeholder="Domyślna strefa czasowa" value="<?php echo(get_misc_value('general_timezone')); ?>">
			<br /><small>Obecny czas systemowy to <b><?php echo(date("Y-m-d H:i:s")); ?></b> (<?php echo(date_default_timezone_get()); ?>).</small>
		</div>
		<div class="dark-box">
			<p>Maszyny sprawdzające</p>
			<label class="switch-checkbox">
				<input type="checkbox" id="general_workers_localonly" name="general_workers_localonly" value="1" <?php echo(boolval(get_misc_value('general_workers_localonly')) ? 'checked' : ''); ?>>
				<span class="switch-checkbox-toggle"></span>
			</label>
			<label for="general_workers_localonly">&emsp;Zezwalaj tylko na ruch lokalny.</label>
			<div id="flex-allowed-IPs-container" style="display: flex; gap: 0.5vmax; flex-wrap: wrap;">
				<?php 
					$allowed_elements = json_decode(get_misc_value('general_workers_allowed_addr'));
					if($allowed_elements != null)
					{
						foreach($allowed_elements as $row)
						{
							echo('<p class="tag" id="allowed_address_'.htmlentities($row).'">'.htmlentities($row).'&emsp;<span onClick="remove_allowed_address(\''.htmlentities($row).'\');">×</span></p>');
						}
					}
				?>
				<p class="tag">
					<textarea id="new_allowed_address" style="background-color: transparent; border: none; font: inherit; height: 1vmax; color: var(--text); outline: none; resize: none;"></textarea><span onClick="add_allowed_address(document.getElementById('new_allowed_address').value);">+</span>
				</p>
			</div>
			<div id="flex-allowed-IPs-container-inputs">
				<?php 
					$allowed_elements = json_decode(get_misc_value('general_workers_allowed_addr'));
					if($allowed_elements != null)
					{
						foreach($allowed_elements as $row)
						{
							echo('<input type="hidden" name="i_allowed_address_'.htmlentities($row).'" id="i_allowed_address_'.htmlentities($row).'" value="'.htmlentities($row).'">');
						}
					}
				?>
			</div>
			<script>
				function add_allowed_address(address)
				{
					document.getElementById('flex-allowed-IPs-container-inputs').innerHTML = document.getElementById('flex-allowed-IPs-container-inputs').innerHTML + "<input type='hidden' name='i_allowed_address_" + address + "' id='i_allowed_address_" + address + "' value='" + address + "'>";
					document.getElementById('flex-allowed-IPs-container').innerHTML += '<p class="tag" id="allowed_address_' + address + '">' + address + '&emsp;<span onClick="remove_allowed_address(\'' + address + '\');">×</span></p>';
				}

				function remove_allowed_address(address)
				{
					document.getElementById(('allowed_address_'+address).toString()).remove();
					document.getElementById(('i_allowed_address_'+address).toString()).remove();
				}
			</script>
		</div>
		<br />
		<input type="submit" class="button" style="margin-right: 5%; font: inherit; border: none;" value="Zapisz konfigurację">
	</form>
	<br />
	<br />
	<br />
	<br />
</div>
<div class="window" id="plugins">
	<h2 class="window_title">Wtyczki systemowe</h2>
	<form method="POST" action="process.php?r=modify_config&category=plugin">
		<div class="dark-box">
			<label class="switch-checkbox">
				<input type="checkbox" name="plugin_portal" value="1" id="plugin_portal" <?php echo(boolval(get_misc_value('plugin_portal')) ? 'checked' : ''); ?>>
				<span class="switch-checkbox-toggle"></span>
			</label>
			<label for="plugin_portal">&emsp;Portal</label>
			<br />
			<br />
			<i class='fas fa-info-circle'></i>&nbsp;&nbsp;Ustawienia modułu znajdziesz w zakładce <a href="?p=portal" style="color: #00b3ff; text-decoration: none;">Zarządzanie portalem</a>.
			<br />
		</div>
		<div class="dark-box">
			<label class="switch-checkbox">
				<input type="checkbox" name="plugin_errors" value="1" id="plugin_errors" <?php echo(boolval(get_misc_value('plugin_errors')) ? 'checked' : ''); ?>>
				<span class="switch-checkbox-toggle"></span>
			</label>
			<label for="plugin_errors">&emsp;Własna strona błędów</label>
			<div class="options">
				<br /><label for="plugin_custom_error_broker_url">Adres URL:&emsp;</label>
				<input type="text" id="plugin_custom_error_broker_url" name="plugin_custom_error_broker_url" placeholder="Adres URL" value="<?php echo(get_misc_value('plugin_custom_error_broker_url')); ?>">
			</div>
		</div>
		<div class="dark-box">
			<label class="switch-checkbox">
				<input type="checkbox" name="plugin_mailing" value="1" id="plugin_mailing" <?php echo(boolval(get_misc_value('plugin_mailing')) ? 'checked' : ''); ?>>
				<span class="switch-checkbox-toggle"></span>
			</label>
			<label for="plugin_mailing">&emsp;Moduł mailingowy</label>
			<div class="options">
				<br /><label for="plugin_mailing_module_host">Adres serwera pocztowego:&emsp;</label>
				<input type="text" id="plugin_mailing_module_host" name="plugin_mailing_module_host" placeholder="Adres serwera pocztowego" value="<?php echo(get_misc_value('plugin_mailing_module_host')); ?>">
				<br /><label for="plugin_mailing_module_port">Port:&emsp;</label>
				<input type="text" id="plugin_mailing_module_port" name="plugin_mailing_module_port" placeholder="Port" value="<?php echo(get_misc_value('plugin_mailing_module_port')); ?>">
				<br /><label for="plugin_mailing_module_username">Użytkownik serwera pocztowego:&emsp;</label>
				<input type="text" id="plugin_mailing_module_username" name="plugin_mailing_module_username" placeholder="Użytkownik serwera pocztowego" value="<?php echo(get_misc_value('plugin_mailing_module_username')); ?>">
				<br /><label for="plugin_mailing_module_password">Hasło serwera pocztowego:&emsp;</label>
				<input type="password" id="plugin_mailing_module_password" name="plugin_mailing_module_password" placeholder="Hasło serwera pocztowego" value="<?php echo(get_misc_value('plugin_mailing_module_password')); ?>">
				<br /><label for="plugin_mailing_module_protocol">Protokół dostępu (SSL, TLS, itp.):&emsp;</label>
				<input type="text" id="plugin_mailing_module_protocol" name="plugin_mailing_module_protocol" placeholder="Protokół dostępu" value="<?php echo(get_misc_value('plugin_mailing_module_protocol')); ?>">
			</div>
		</div>
		<div class="dark-box">
			<label class="switch-checkbox">
				<input type="checkbox" name="plugin_debugging" value="1" id="plugin_debugging" <?php echo(boolval(get_misc_value('plugin_debugging')) ? 'checked' : ''); ?>>
				<span class="switch-checkbox-toggle"></span>
			</label>
			<label for="plugin_debugging">&emsp;Zaawansowany debugging</label>
		</div>
		<br />
		<input type="submit" class="button" style="margin-right: 5%; font: inherit; border: none;" value="Zapisz wtyczki syst.">
	</form>
	<br />
	<br />
	<h2 class="window_title">Wtyczki społeczności</h2>
	<form method="POST" action="process.php?r=modify_config&category=community_plugin">
		<?php
			$plugins = array_slice(scandir("../../include/plugins/"),2);
			
			foreach($plugins as $plugin)
			{
				echo('<div class="dark-box">
					<label class="switch-checkbox">
						<input type="checkbox" name="community_plugin_'.htmlentities($plugin).'" value="1" id="community_plugin_'.htmlentities($plugin).'" '.(boolval(get_misc_value('community_plugin_'.htmlentities($plugin))) ? 'checked' : '').'>
						<span class="switch-checkbox-toggle"></span>
					</label>
					<label for="community_plugin_'.htmlentities($plugin).'">&emsp;Plugin zewnętrzny: <b>'.htmlentities($plugin).'</b></label>
					<a href="process.php?r=modify_config&call=plugin_uninstall&plugin_name='.$plugin.'" class="remove_plugin"><i class="fa fa-trash"></i></a>');
					if(!include_plugins_for("settings_configuration", $plugin)) echo("&emsp;<small>Wczytywanie wtyczki nie powiodło się.</small>");
					echo('	<br style="clear: both;"/>
				</div>');
			}

			if(count($plugins)==0) {
				echo('<center style="color: var(--text);">
					<br />
					<i class="fa fa-search" style="font-size: 5vmax;"></i>
					<p>Naciśnij <code>Znajdź wtyczki</code> by pobrać nowe modyfikacje.</p>
					<br />
				</center>');
			}
		?>
		<br />
		<input type="submit" class="button" style="margin-right: 5%; font: inherit; border: none;" value="Zapisz wtyczki społ.">
		<a class="button" onClick="document.getElementById('plugins_dialog').style.display = 'flex';" style="margin-right: 0.5%; font: inherit; border: none;">Znajdź wtyczki</a>
	</form>
	<br />
	<br />
	<br />
	<br />
</div>
<div class="window" id="users">
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