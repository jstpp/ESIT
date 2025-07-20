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

	.window .window_title {
		margin-left: 5%;
		margin-top: 1.5vw;
	}

	.window #results {
		width: 90%;
		margin-left: 5%;
		user-select: none;
	}
	.window #results a {
		text-decoration: none;
		color: rgb(0, 179, 255);
	}
	.window #results td {
		border-top: 0.1vw solid gray;
		padding: 0.5vw 0.5vw;
	}
	.window #results td a {
		font-weight: bold;
		text-align: center;
		transition: 0.3s;
		cursor: pointer;
	}
	.window #results tr {
		transition: 0.2s;
		cursor: default;
	}
	.window #results tr:hover {
		background-color: var(--container-hover-bg);
	}
</style>

<center>
	<h1>Moje rozwiązania</h1>
</center>
<div class="window">
	<h2 class="window_title">Moje rozwiązania</h2>
	<table id="results">
		<?php
			$db_query = $pdo->prepare('SELECT SUBMISSIONS.SUBMISSION_ID AS id, SUBMISSIONS.mode AS mode, SUBMISSIONS.submission_lang AS lang, SUBMISSIONS.verification_time, SUBMISSIONS.submission_time AS submission_time, SUBMISSIONS.score AS score, SUBMISSIONS.score_percentage AS score_percentage, PROBLEMS.title AS title, PROBLEMS.type AS type, PROBLEMS.maxpoints AS max_pts, PROBLEMS.PROBLEM_ID AS problem_id FROM SUBMISSIONS INNER JOIN PROBLEMS ON SUBMISSIONS.problem_id=PROBLEMS.PROBLEM_ID WHERE SUBMISSIONS.user_id=:uid ORDER BY SUBMISSIONS.submission_time DESC');
			$db_query->execute(['uid' => $_SESSION['AUTH_ID']]);

			$isfound = 0;
			while($row = $db_query->fetch())
			{
				$isfound++;
				if($row['score_percentage']==0)
				{
					$gradient = "linear-gradient(to left,#ff3d6e 0%,transparent 50%);";
					$percentage = $row['score_percentage']."%";
					$status = "Całkowicie niepoprawne";
				} else if ($row['score_percentage']==100)
				{
					$gradient = "linear-gradient(to left,#00d10a 0%,transparent 50%);";
					$percentage = $row['score_percentage']."%";
					$status = "Bez błędów";
				} else if ($row['score_percentage']==-1)
				{
					$gradient = "linear-gradient(to left,gray 0%,transparent 50%);";
					$percentage = "...";
					$status = "W kolejce";
				} else {
					$gradient = "linear-gradient(to left,#8eed28 0%,transparent 50%);";
					$percentage = $row['score_percentage']."%";
					$status = "Częściowo poprawne";
				}

				if($row['type']==1)
				{
					$problemtype = "<i class='fas fa-cloud'></i>&nbsp;Algorytmiczne";
					$resultdest = "algresult";
				} else if($row['type']==2)
				{
					$problemtype = "<i class='fas fa-flag'></i></i>&nbsp;&nbsp;Capture The Flag";
					$resultdest = "ctfresult";
				} else if($row['type']==3)
				{
					$problemtype = "<i class='fas fa-check-square'></i>&nbsp;&nbsp;Jednokrotnego wyboru";
					$resultdest = "testresult";
				} else if($row['type']==4)
				{
					$problemtype = "<i class='fas fa-check-double'></i>&nbsp;&nbsp;Wielokrotnego wyboru";
					$resultdest = "testresult";
				} else if($row['type']==5)
				{
					$problemtype = "<i class='fa fa-edit'></i>&nbsp;&nbsp;Formularz";
					$resultdest = "formresult";
				} else {
					$problemtype = "Nieznany typ";
				}

				echo('<tr>
				<td><a href="?p='.$resultdest.'&sid='.$row['id'].'">Szczegóły</a></td>
				<td>'.$row['submission_time'].'</td>
				<td><a href="?p=quest&id='.$row['problem_id'].'">'.$row['title'].'</a></td>
				<td>'.$problemtype.'</td>
				<td>'.htmlentities($row['lang']).'</td>
				<td style="background-image: '.$gradient.'">'.$status.'</td>');
				if ($row['score_percentage']!=-1) {
					echo('
					<td>'.$row['score'].'/'.$row['max_pts'].'</td>
					<td style="background-image: '.$gradient.'">'.$percentage.'</td>');
				}
				echo('</tr>');
			}
			if($isfound==0)
			{
				echo("<center>Jeszcze tu niczego nie ma!</center>");
			}
		?>
	</table>
	<br />
	<br />
</div>
<br />
<br />