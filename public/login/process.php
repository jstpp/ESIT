<?php
	if(isset($_GET['s']))
	{
		include(__DIR__.'/../../include/config/config_init.php');
		if ($_GET['s']=="logout")
		{
			include(__DIR__.'/../../include/login/logout.php');
		} else if ($_GET['s']=="auth")
		{
			include(__DIR__.'/../../include/login/auth.php');
		}
	}
?>
<style>
	* {
		background-color: #3e4145;
	}
</style>