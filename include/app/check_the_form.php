<?php
	if(isset($_GET['sid']))
	{
		if($_SESSION['AUTH_LEVEL']<=3)
		{
			$db_query = $pdo->prepare('SELECT DISTINCT * FROM SUBMISSIONS INNER JOIN PROBLEMS ON SUBMISSIONS.problem_id=PROBLEMS.PROBLEM_ID INNER JOIN USERS ON USERS.USER_ID=SUBMISSIONS.user_id WHERE SUBMISSIONS.SUBMISSION_ID=:sid');
			$db_query->execute(['sid' => $_GET['sid']]);
		} else {
			kick();
		}

		$row = $db_query->fetch();

	} else {
		kick();
	}
?>
<style>
	.window {
		width: 95%;
		margin-left: 2.5%;
		margin-top: 1vw;

		background-color: var(--container-bg);;

		border: 0.2vw solid #2a2c2e;
		border-radius: 1vw;
		transition: 0.2s;
		user-select: none;
	}

	.window .window_title {
		margin-left: 5%;
		margin-top: 1.5vw;
	}
	.window .forminput {
		border: 0; 
		padding: 0.75vw 1.25vw; 
		color: #dae2e6; 
		background-color: #2a2c2e; 
		font-family: inherit;
		outline: none;
	}
	.window .forminput_a {
		border: 0; 
		padding: 0.75vw 2.5%; 
		color: #dae2e6; 
		background-color: #2a2c2e; 
		font-family: inherit;
		cursor: pointer;
		transition: 0.4s;
		text-decoration: none;
	}
	.window .forminput_a:hover {
		background-color: #3e4145;
	}
</style>

<script>
	MathJax = {
	tex: {
		inlineMath: [['$', '$']]
	}
	};
</script>
<script id="MathJax-script" async
  src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js">
</script>

<center>
	<h1>Właśnie sprawdzasz pracę</h1>
</center>

<div class="window">
	<h3 class="window_title">Odpowiedź użytkownika <?php echo($row['username']); ?></h3>
	<br />
	<div style="user-select: text; margin-left: 5%; width: 88%; padding: 1% 1%; background-color: #dae2e6; color: #2a2c2e;">
		<?php
			echo($row['content']);
		?>
	</div>
	<br />
	<br />
</div>
<div class="window">
	<h3 class="window_title">Twoja ocena</h3>
	<form id="rate_form" method="POST" action="process.php?r=check_form&id=<?php echo($row['SUBMISSION_ID']); ?>">
		<input type="number" class="forminput" id="pts" name="form_pts" style="margin-left: 5%;" min="0" max="<?php echo($row['maxpoints']); ?>"/>&emsp;/&emsp;<?php echo($row['maxpoints']); ?>
		<br />
		<h3 class="window_title">Ewentualny komentarz</h3>
		<textarea name="form_comment" class="forminput" style="margin-left: 5%; width: 50%; border-radius: 0.3vmax;"></textarea>
		<br />
		<br />
	</form>
	<a class="forminput_a" style="margin-left: 5%;" onClick="document.getElementById('rate_form').submit();">Zatwierdź ocenę</a>
	<br />
	<br />
	<br />
</div>
<div class="window">
	<h3 class="window_title">Treść zadania</h3>
	<br />
	<iframe src="content/quests/<?php echo($row['PROBLEM_ID']); ?>/pdf/<?php echo($row['PROBLEM_ID']); ?>.pdf" style="width: 90%; margin-left: 5%; height: 85vh; border: 0;"></iframe>
	<br />
	<br />
	<br />
</div>
<br />
<br />