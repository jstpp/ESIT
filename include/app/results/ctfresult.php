<?php
	if($_SESSION['AUTH_LEVEL']<5)
	{
		$db_query = $pdo->prepare('SELECT DISTINCT * FROM SUBMISSIONS INNER JOIN PROBLEMS ON SUBMISSIONS.problem_id=PROBLEMS.PROBLEM_ID WHERE SUBMISSIONS.SUBMISSION_ID=:sid');
		$db_query->execute(['sid' => $_GET['sid']]);
	} else {
		$db_query = $pdo->prepare('SELECT DISTINCT * FROM SUBMISSIONS INNER JOIN PROBLEMS ON SUBMISSIONS.problem_id=PROBLEMS.PROBLEM_ID WHERE SUBMISSIONS.SUBMISSION_ID=:sid AND SUBMISSIONS.user_id=:uid');
		$db_query->execute(['sid' => $_GET['sid'], 'uid' => $_SESSION['AUTH_ID']]);
	}

	$row = $db_query->fetch();

	if(strtotime($row['result_publish_time'])<strtotime("now"))
	{
		if($row['score']=='0')
		{
			$comment = "Nie udało Ci się przesłać poprawnej flagi. Szukaj dalej!";
			$gradient = "linear-gradient(to left,#ff3d6e 0%,transparent 50%);";
		} else {
			$comment = "Udało Ci się przesłać poprawną flagę. Cieszymy się Twoim szczęściem!";
			$gradient = "linear-gradient(to left,#00d10a 0%,transparent 50%);";
		}
	} else {
		$comment = "Wynik weryfikacji twojej flagi nie jest jeszcze dostępny.";
		$gradient = "linear-gradient(to left, gray 0%,transparent 50%);";
	}
?>
<style>
	.window {
		width: 95%;
		margin-left: 2.5%;
		margin-top: 1vw;

		background-color: var(--container-bg);

		border: 0.2vw solid #2a2c2e;
		border-radius: 1vw;
		transition: 0.2s;
		user-select: none;
	}

	.window .window_title {
		margin-left: 5%;
		margin-top: 1.5vw;
	}

	.window .results {
		width: 90%;
		margin-left: 5%;
		user-select: none;
	}
	.window .results td {
		border-top: 0.1vw solid gray;
		padding: 0.5vw 0.5vw;
	}
	.window .results td a {
		font-weight: bold;
		text-align: center;
		transition: 0.3s;
		cursor: pointer;
	}
	.window .results tr {
		transition: 0.2s;
		cursor: default;
	}
	.window .results ul {
		list-style-type: none;
		padding: 0;
		margin: 0;
	}
</style>

<center>
	<h1>Raport z zadania typu CTF</h1>
</center>
<div class="window">
	<h2 class="window_title"><?php echo($row['title']); ?></h2>
	<p style="margin-left: 5%;"><i class='fas fa-info-circle'></i>&nbsp;&nbsp;<?php echo($comment); ?></p>
	<table class="results" style="width: 90%; margin-right: 5%; margin-top: 4vmax;">
		<tr>
			<td><?php echo($row['submission_time']); ?></td>
			<td><i class='fas fa-flag'></i></i>&nbsp;&nbsp;Capture The Flag</td>
			<td><?php if(strtotime($row['result_publish_time'])<strtotime("now")) { echo($row['score']); } else { echo("???"); }?>/<?php echo($row['maxpoints']); ?></td>
			<td style="background-image: <?php echo($gradient); ?>"><?php if(strtotime($row['result_publish_time'])<strtotime("now")) { echo($row['score_percentage']); } else { echo("???"); }?>%</td>
		</tr>
	</table>
	<br style="clear: both;" />
	<br />
	<br />
</div>
<br />
<br />