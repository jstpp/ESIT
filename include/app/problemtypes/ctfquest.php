<style>
	.window a {
		color: var(--highlight-color);
		font-weight: bold;
		text-decoration: none;
	}

	.window .forminput {
		border: 0; 
		padding: 0.75vw 1.25vw; 
		color: var(--text); 
		background-color: var(--container-hover-bg); 
		font-family: inherit;
		outline: none;
	}
	.window .forminput_a {
		border: 0; 
		padding: 0.75vw 2.5%; 
		color: var(--text); 
		background-color: var(--container-hover-bg); 
		font-family: inherit;
		cursor: pointer;
		transition: 0.4s;
		text-decoration: none;
	}
	.window .forminput_a:hover {
		background-color: var(--bg);
	}

	.window input, .window select {
		font: inherit;
	}
</style>
<div class="window">
	<p style="margin-left: 5%;">
		<?php
			$db_query = $pdo->prepare('SELECT COUNT(*) AS count FROM SUBMISSIONS WHERE problem_id=:pid AND user_id=:uid AND mode=2');
			$db_query->execute(['pid' => filter_var($_GET['id'], FILTER_VALIDATE_INT), 'uid' => $_SESSION['AUTH_ID']]);

			$count = $db_query->fetch()['count'];

			if($maxattempts-$count>1)
			{
				$howmanytimes = __("You can submit your solution ")." <b>".($maxattempts-$count)." ".__("times")."</b>.";
			} else if ($maxattempts-$count==1)
			{
				$howmanytimes = __("You can submit your solution <b>once only</b>.");
			} else {
				$howmanytimes = __("You can't submit new solutions.");
			}
		?>
		<i class='fas fa-info-circle'></i>&emsp;<?php echo($howmanytimes); ?><br />
	</p>
</div>
<div class="window">
	<br />
	<br />
	<center>
		<a class="forminput_a" href="process.php?r=get_content&mode=ctffile&cid=<?php echo(filter_var($problemid, FILTER_VALIDATE_INT)); ?>" download>Pobierz plik</a>
	</center>
	<br />
	<br />
</div>
<div class="window">
	<h2 style="margin-left: 5%;"><?php echo(__("Your solution")); ?></h2>
	<form id="ctf_form" method="POST" action="process.php?r=verify_ctf&id=<?php echo($problemid); ?>">
		<input type="text" name="ctf_flag" id="ctf_flag" class="forminput" style="margin-left: 5%; width: 85%;" placeholder="$_{TWOJA_FLAGA}_$"/>
		<input name="csrf_token" type="hidden" value="<?php echo($_SESSION['CSRF_TOKEN']); ?>" />
		<br />
		<br />
		<a class="forminput_a" onClick="document.getElementById('ctf_form').submit();" style="font-weight: normal; float: right; margin-right: 6.75%;"><?php echo(__("Submit your solution")); ?></a>
	</form>
	<br style="clear: both;" />
	<br />
	<br />
</div>
<br />
<br />