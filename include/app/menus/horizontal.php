<style> 
	#horizontal_menu {
		margin: 0;
		width: 100%;
		height: 3vw;
		top: 0;

		z-index: 5;
		position: fixed;

		background-color: var(--horizontal-menu-bg);
	}
	#horizontal_menu a {
		margin: 0;
		padding: 0.8vw 1.2vw;

		display: block;
		float: right;
		text-decoration: none;

		color: var(--horizontal-menu-text);
		text-align: center;
		transition: 0.2s;
		cursor: pointer;
		user-select: none;
	}
	#horizontal_menu a:hover {
		margin: 0;

		background-color: var(--vertical-menu-bg);
	}
</style>
<div id="horizontal_menu">
	<a href="../login/process.php?s=logout"><i class='fas fa-door-open'></i>&emsp;Wyloguj się</a>
	<a onClick="document.getElementById('notifications_menu').style.display = 'block';"><i class='fa fa-bell'></i>&emsp;Powiadomienia</a>
	<?php
		if(boolval(get_misc_value('plugin_portal')))
		{
			echo('<a href="../index.php"><i class=\'fa fa-bookmark\'></i>&emsp;Portal</a>');
		}
	?>
</div>