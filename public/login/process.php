<?php
	if(isset($_GET['s']))
	{
		include(__DIR__.'/../../include/app/core.php');
		
		if ($_GET['s']=="logout")
		{
			include(__DIR__.'/../../include/login/logout.php');
		} else if ($_GET['s']=="auth")
		{
			include(__DIR__.'/../../include/login/auth.php');
		} else if ($_GET['s']=="passrecovery_mail")
		{
			include(__DIR__.'/../../include/login/passrecovery_mail.php');
		} else if ($_GET['s']=="passrecovery_pass")
		{
			include(__DIR__.'/../../include/login/passrecovery_pass.php');
		}
	}
?>
<style>
	* {
		background-color: #3e4145;
	}
</style>