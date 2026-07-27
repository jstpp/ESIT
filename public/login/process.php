<?php
	include(__DIR__.'/../../include/app/core.php');
	if(!isset($_GET['s'])) kick();
		
	if ($_GET['s']=="logout")
	{
		include(__DIR__.'/../../include/login/logout.php');
	} else if ($_GET['s']=="auth")
	{
		if(!api_rate_limit_tick(150, 600)) {
			header('HTTP/1.1 429 Too Many Requests');
			die;
		}
		include(__DIR__.'/../../include/login/auth.php');
	} else if ($_GET['s']=="passrecovery_mail")
	{
		if(!api_rate_limit_tick(180, 600)) {
			header('HTTP/1.1 429 Too Many Requests');
			die;
		}
		include(__DIR__.'/../../include/login/passrecovery_mail.php');
	} else if ($_GET['s']=="passrecovery_pass")
	{
		if(!api_rate_limit_tick(180, 600)) {
			header('HTTP/1.1 429 Too Many Requests');
			die;
		}
		include(__DIR__.'/../../include/login/passrecovery_pass.php');
	}
?>
<style>
	* {
		background-color: rgba(39, 55, 71, 1);
	}
</style>