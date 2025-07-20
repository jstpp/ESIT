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
		cursor: pointer;
	}
	.window:hover {
		background-color: var(--container-hover-bg);
	}

	.window .window_title {
		margin-left: 5%;
		margin-top: 1.5vw;
	}
</style>

<center>
	<h1>Archiwalne zbiory zadań</h1>
</center>
<?php
	$db_query = $pdo->prepare('SELECT PROBLEMSETS.SET_ID AS sid, PROBLEMSETS.title AS title, USERS.name AS name, USERS.surname AS surname FROM PROBLEMSETS INNER JOIN USERS ON PROBLEMSETS.author_id=USERS.USER_ID WHERE PROBLEMSETS.isarchived=1 AND :current_time>=PROBLEMSETS.publish_time ORDER BY USER_ID DESC');
    $db_query->execute(['current_time' => date('Y/m/d H:i:s')]);

	$isfound = 0;
    while($row = $db_query->fetch())
    {
		echo('<div class="window" onClick="window.location.href = \'?p=set&id='.$row['sid'].'\';">
		<h2 class="window_title">'.$row['title'].'</h2><i style="font-size: 0.6vw; color: gray; margin-left: 5%; margin-top: -0.5vw; display: block;">Kliknij, by przejść do zbioru</i>
		<p style="margin-left: 5%;">
			<i class=\'fas fa-user\'></i>&nbsp;&nbsp;Autor: <b>'.$row['name']." ".$row['surname'].'</b><br />
		</p>
		<br />
		</div>');
		$isfound++;
	}

	if($isfound==0)
	{
		echo("<center>Jeszcze tu niczego nie ma!</center>");
	}
?>
<br />
<br />