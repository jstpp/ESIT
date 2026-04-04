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
	<p style="margin-left: 0.8vw; margin-top: 5vw;">
		<b><?php echo($_SESSION['AUTH_NAME']) ?> <?php echo($_SESSION['AUTH_SURNAME']) ?></b>
		<br />
		<span style="font-size: 0.8vw;"><?php echo($_SESSION['AUTH_USERNAME']) ?> • <?php echo($_SESSION['AUTH_ROLE']) ?></span>
	</p>
	<br />
	<a href="?p=dashboard" id="dashboard"><i class='fas fa-book-open'></i>&emsp;&nbsp;Dashboard</a>
	<?php
		if(has_a_priority(3))
		{
			echo('<p class="category_title">Administracja</p>');
			echo('<a href="?p=admin" id="admin"><i class=\'fas fa-school\'></i>&emsp;Konfiguracja</a>');
			echo('<a href="?p=diagnostics" id="diagnostics"><i class=\'fa fa-dashboard\'></i>&emsp;&nbsp;Diagnostyka</a>');
			if(boolval(get_misc_value('plugin_portal')))
			{
				echo('<a href="?p=portal" id="portal"><i class=\'fa fa-bank\'></i>&emsp;&nbsp;Zarządzanie portalem</a>');
			}
		}
	?>
	<p class="category_title">Zbiory zadań</p>
	<a href="?p=sets" id="sets"><i class='fas fa-folder-open'></i>&emsp;Aktywne zbiory zadań</a>
	<a href="?p=archive" id="archive"><i class='far fa-folder-open'></i>&emsp;Archiwalne zbiory zadań</a>
	<a href="?p=mysolutions" id="mysolutions"><i class='fas fa-paper-plane'></i>&emsp;Moje rozwiązania</a>
	<?php
		if(has_a_priority(4))
		{
			echo('<a href="?p=myexamsadmin" id="myexamsadmin"><i class=\'fas fa-file-alt\'></i>&emsp;&nbsp;Panel egzaminatora</a>');
		}
	?>
	<p class="category_title">Ustawienia użytkownika</p>
	<a href="?p=settings" id="settings"><i class='fas fa-eye'></i>&emsp;Ustawienia konta</a>
</div>