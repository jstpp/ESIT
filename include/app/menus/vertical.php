<style> 
	:root {
		--vertical-menu-bg: #313136;
		--vertical-menu-text: #dae2e6;
	}

	[data-theme="light"] {
		--vertical-menu-bg: #c8c8c8;
		--vertical-menu-text: #313136;
	}

	[data-theme="dark"] {
		--vertical-menu-bg: #313136;
		--vertical-menu-text: #dae2e6;
	}
	#vertical_menu {
		margin: 0;
		height: 100vh;
		width: 20vw;
		top: 0;
		left: 0;

		z-index: 4;
		position: fixed;
		overflow: auto;

		background-color: var(--vertical-menu-bg);
	}

	#vertical_menu a {
		padding: 1vw 1vw;
		display: flex;

		text-decoration: none;
		color: inherit;
		cursor: pointer;
		transition: 0.2s;
		user-select: none;
	}
	#vertical_menu a:hover {
		background-color: #2a2c2e;
		color: #00b3ff;
	}

	#vertical_menu .category_title {
		margin-top: 1.4vw;
		margin-left: 1vw;
		user-select: none;

		font-weight: bold;
	}
</style>
<div id="vertical_menu">
	<p style="margin-left: 1vw; margin-top: 5vw;">
		<b><?php echo($_SESSION['AUTH_NAME']) ?> <?php echo($_SESSION['AUTH_SURNAME']) ?></b>
		<br />
		<span style="font-size: 0.8vw;"><?php echo($_SESSION['AUTH_USERNAME']) ?> • <?php echo($_SESSION['AUTH_ROLE']) ?></span>
	</p>
	<br />
	<a href="?p=dashboard" id="dashboard"><i class='fas fa-book-open'></i>&emsp;Dashboard</a>
	<?php
		if(has_a_priority(3))
		{
			echo('<a href="?p=admin" id="admin"><i class=\'fas fa-school\'></i>&emsp;Administracja</a>
	<a href="?p=portal" id="portal"><i class=\'fa fa-bank\'></i>&emsp;Zarządzanie portalem</a>');
		}
	?>
	<p class="category_title">Zbiory zadań</p>
	<a href="?p=sets" id="sets"><i class='fas fa-folder-open'></i>&emsp;Aktywne zbiory zadań</a>
	<a href="?p=archive" id="archive"><i class='far fa-folder-open'></i>&emsp;Archiwalne zbiory zadań</a>
	<a href="?p=mysolutions" id="mysolutions"><i class='fas fa-paper-plane'></i>&emsp;Moje rozwiązania</a>
	<?php
		if(has_a_priority(4))
		{
			echo('<a href="?p=myexamsadmin" id="myexamsadmin"><i class=\'fas fa-file-alt\'></i>&emsp;Panel egzaminatora</a>');
		}
	?>

	<p class="category_title">Ustawienia</p>
	<a href="?p=settings" id="settings"><i class='fas fa-eye'></i>&emsp;Ustawienia konta</a>
</div>