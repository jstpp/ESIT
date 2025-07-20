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

	.window:hover {
		background-color: #2a2c2e;
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
	.window .results tr:hover {
		background-color: #2a2c2e;
	}
</style>

<center>
	<h1>Formularze oczekujące na sprawdzenie</h1>
</center>
<?php
	$db_query = $pdo->prepare('SELECT PROBLEMS.title AS title, SUBMISSIONS.content AS content, SUBMISSIONS.SUBMISSION_ID AS subid, USERS.username AS username FROM SUBMISSIONS INNER JOIN USERS ON SUBMISSIONS.user_id=USERS.USER_ID INNER JOIN PROBLEMS ON SUBMISSIONS.problem_id=PROBLEMS.PROBLEM_ID WHERE SUBMISSIONS.content<>"-" AND SUBMISSIONS.score=-1 ORDER BY SUBMISSIONS.submission_time DESC');
    $db_query->execute();

	$count = 0;
    while($row = $db_query->fetch())
    {
		echo('<div class="window" onClick="window.location.href = \'?p=check_the_form&sid='.$row['subid'].'\';" style="cursor: pointer;">
		<h2 class="window_title">'.$row['title'].'</h2><i style="font-size: 0.6vw; color: gray; margin-left: 5%; margin-top: -0.5vw; display: block;">Kliknij, by zacząć sprawdzać</i>
		<p style="margin-left: 5%;">
			<i class=\'fas fa-user\'></i>&nbsp;&nbsp;Autor: <b>'.$row['username'].'</b><br />
			<i class=\'fas fa-file\'></i>&nbsp;&nbsp;Te rozwiązanie ma około '.strlen(strip_tags(preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '',$row['content']))).' znaków</b><br />
		</p>
		<br />
		</div>');
		$count++;
	}
	if($count==0)
	{
		echo("<center><i>Nie ma niczego do sprawdzenia!</i></center>");
	}
?>
<br />
<br />