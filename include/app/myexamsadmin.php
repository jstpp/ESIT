<style>
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
		echo('
		<div style="margin-left: auto; margin-right: auto; margin-top: 5vmax; text-align: center; display: flex; flex-direction: column; justify-content: cetner; width: 30%; padding: 3vmax; background-color: var(--container-bg); border: 0.2vw solid var(--container-hover-bg); border-radius: 1vw;">
			<i class="fa fa-hourglass-3" style="font-size: 7vmax;"></i>
			<center style="margin-top: 2vmax; user-select: none;"><i>Nie ma niczego do sprawdzenia!</i></center>
		</div>
		');
	}
?>
<br />
<br />