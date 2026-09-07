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
<div id="vertical_menu">
	<div style="margin-left: 0.8vw; margin-top: 5vw; display: flex; gap: 1vmax;">
		<img src="https://api.dicebear.com/10.x/identicon/svg?seed=<?php echo($_SESSION['AUTH_USERNAME']) ?>" onClick="document.getElementById('profile_background_pane').style.display = 'flex';" style="cursor: pointer; width: 2.5vmax; border-radius: 1.25vmax; background-color: var(--text);"/>
		<div>
			<b><?php echo($_SESSION['AUTH_NAME']) ?> <?php echo($_SESSION['AUTH_SURNAME']) ?></b>
			<br />
			<span style="font-size: 0.8vw;"><?php echo($_SESSION['AUTH_USERNAME']) ?> • <?php echo($_SESSION['AUTH_ROLE']) ?></span>
		</div>
	</div>
	<br />
	<br />
	<a href="?p=dashboard" id="dashboard"><i class='fas fa-compass'></i>&emsp;<?php echo(__("Dashboard")); ?></a>
	<a href="?p=settings" id="settings"><i class='fas fa-address-card'></i>&emsp;<?php echo(__("Account settings")); ?></a>
	<?php
		if(has_a_priority(3))
		{
			echo('<p class="category_title">'.__("Management").'</p>');
			echo('<a href="?p=admin" id="admin"><i class=\'fas fa-tools\'></i>&emsp;'.__("Configuration").'</a>');
			if(boolval(get_misc_value('plugin_portal')))
			{
				echo('<a href="?p=portal" id="portal"><i class=\'fas fa-pen-nib\'></i>&emsp;'.__("Portal management").'</a>');
			}
			echo('<a href="?p=diagnostics" id="diagnostics"><i class=\'fa fa-dashboard\'></i>&emsp;'.__("Diagnostics").'</a>');
			echo('<a href="?p=logs" id="logs"><i class=\'fas fas fa-stream\'></i>&emsp;'.__("Logs").'</a>');
			include_plugins_for("vertical_menu_administration");
		}
	?>
	<p class="category_title"><?php echo(__("Content")); ?></p>
	<a href="?p=channels" id="contentsets"><i class='fas fa-pencil-ruler'></i>&emsp;<?php echo(__("Discover")); ?></a>
	<a href="?p=mysolutions" id="mysolutions"><i class='fas fa-paper-plane'></i>&emsp;<?php echo(__("My solutions")); ?></a>
	<?php
		include_plugins_for("vertical_menu_problemsets");
	?>
	<?php
		if(has_a_priority(4))
		{
			echo('<a href="?p=myexamsadmin" id="myexamsadmin"><i class=\'fas fa-coffee\'></i>&nbsp;&nbsp;&nbsp;'.__("Creators' center").'</a>');
		}
	?>

	<?php
		include_plugins_for("vertical_menu");
	?>

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
</div>