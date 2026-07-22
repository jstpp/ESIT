<style> 
	#vertical_menu {
		margin: 0;
		height: 100vh;
		width: 18vw;
		top: 0;
		left: 0;

		z-index: 4;
		position: fixed;
		overflow: auto;

		background-color: var(--vertical-menu-bg);
	}

	#vertical_menu a {
		padding: 0.6vw 1vw;
		display: flex;

		text-decoration: none;
		align-items: center;
		color: inherit;
		cursor: pointer;
		transition: 0.2s;
		user-select: none;
	}
	#vertical_menu a:hover {
		background-color: var(--container-hover-bg);
		color: #00b3ff;
	}

	#vertical_menu .category_title {
		margin-top: 1.2vw;
		margin-left: 1vw;
		user-select: none;

		font-weight: bold;
	}
</style>
<div id="vertical_menu">
	<div style="margin-left: 0.8vw; margin-top: 5vw; display: flex; gap: 1vmax;">
		<img src="https://api.dicebear.com/10.x/identicon/svg?seed=<?php echo($_SESSION['AUTH_USERNAME']) ?>" style="width: 2.5vmax; border-radius: 1.25vmax; background-color: var(--text);"/>
		<div>
			<b><?php echo($_SESSION['AUTH_NAME']) ?> <?php echo($_SESSION['AUTH_SURNAME']) ?></b>
			<br />
			<span style="font-size: 0.8vw;"><?php echo($_SESSION['AUTH_USERNAME']) ?> • <?php echo($_SESSION['AUTH_ROLE']) ?></span>
		</div>
	</div>
	<br />
	<br />
	<a href="?p=dashboard" id="dashboard"><i class='fas fa-compass'></i>&emsp;Dashboard</a>
	<a href="?p=settings" id="settings"><i class='fas fa-address-card'></i>&emsp;Ustawienia konta</a>
	<?php
		if(has_a_priority(3))
		{
			echo('<p class="category_title">Administracja</p>');
			echo('<a href="?p=admin" id="admin"><i class=\'fas fa-tools\'></i>&emsp;Konfiguracja</a>');
			if(boolval(get_misc_value('plugin_portal')))
			{
				echo('<a href="?p=portal" id="portal"><i class=\'fas fa-pen-nib\'></i>&emsp;Zarządzanie portalem</a>');
			}
			echo('<a href="?p=diagnostics" id="diagnostics"><i class=\'fa fa-dashboard\'></i>&emsp;Diagnostyka</a>');
			echo('<a href="?p=logs" id="logs"><i class=\'fas fas fa-stream\'></i>&emsp;Dziennik zdarzeń</a>');
			include_plugins_for("vertical_menu_administration");
		}
	?>
	<p class="category_title">Treści</p>
	<a href="?p=contentsets" id="contentsets"><i class='fas fa-pencil-ruler'></i>&emsp;Zbiory treści</a>
	<a href="?p=mysolutions" id="mysolutions"><i class='fas fa-paper-plane'></i>&emsp;Moje rozwiązania</a>
	<?php
		include_plugins_for("vertical_menu_problemsets");
	?>
	<?php
		if(has_a_priority(4))
		{
			echo('<a href="?p=myexamsadmin" id="myexamsadmin"><i class=\'fas fa-coffee\'></i>&emsp;Centrum twórców</a>');
		}
	?>

	<?php
		include_plugins_for("vertical_menu");
	?>
</div>