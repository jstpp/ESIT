<style>
	.window {
		width: 100%;
		margin-top: 1vw;

		background-color: var(--container-bg);

		border: 0.2vw solid var(--container-hover-bg);
		border-radius: 1vw;
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

	.set_link {
		padding: 1vw 1vw;
		width: 90%;
		margin-left: 4%;
		
		display: flex;

		font-weight: bold;
		color: inherit;
		text-decoration: none;
		user-select: none;
		transition: 0.2s;
		cursor: pointer;
	}
	.set_link:hover {
		background-color: var(--container-hover-bg);
	}
	
	.window .news {
		background-color: var(--container-hover-bg);
		width: 85%;
		margin-left: 5%;
		margin-top: 0.5vw;
		padding: 1% 2%;
	}

	.news img {
		max-width: 100%;
	}

	#dashboard_badges img {
		width: 25%;
	}

	#dashboard_propositions_bar {
		width: 100%;
		display: flex;
		gap: 0.5vmax;
		justify-content: center;
		flex-direction: column;
	}

	.dashboard_content_set {
		width: 88%;
		transition: 0.3s;
		margin-left: 5%;
		min-height: 10vmax;
		border: 0.2vw solid var(--container-hover-bg);
		border-radius: 0.5vw;
		background-color: var(--bg);
		overflow: hidden;
	}

	.dashboard_content_set:hover {
		border: 0.2vw solid rgb(0, 179, 255);
		cursor: pointer;
	}

	.dashboard_content_set_metadata {
		padding-left: 1vmax;
		padding-right: 1vmax;
		color: white;
	}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<center>
	<h1 style="font-size: 3.5vw; user-select: none; background: linear-gradient(315deg, rgba(0, 179, 255, 1) 0%, var(--text) 60%); -webkit-background-clip: text; color: transparent;">Witaj, <?php echo(htmlentities($_SESSION['AUTH_NAME'])); ?>!</h1>
</center>
<div style="display: flex; margin: 2.5%; gap: 2.5%;">
	<div style="min-width: 72%;">
		<div class="window">
			<h2 class="window_title">Moje ostatnie rozwiązania</h2>
			<table id="results">
				<?php
					$db_query = $pdo->prepare('SELECT SUBMISSIONS.SUBMISSION_ID AS id, SUBMISSIONS.mode AS mode, SUBMISSIONS.verification_time, SUBMISSIONS.submission_time AS submission_time, SUBMISSIONS.score AS score, SUBMISSIONS.score_percentage AS score_percentage, PROBLEMS.title AS title, PROBLEMS.type AS type, PROBLEMS.maxpoints AS max_pts, PROBLEMS.PROBLEM_ID AS problem_id, PROBLEMS.result_publish_time AS result_publish_time FROM SUBMISSIONS INNER JOIN PROBLEMS ON SUBMISSIONS.problem_id=PROBLEMS.PROBLEM_ID WHERE SUBMISSIONS.user_id=:uid ORDER BY SUBMISSIONS.submission_time DESC LIMIT 4');
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
						<td>'.$problemtype.'</td>');
						if ($row['score_percentage']!=-1 and strtotime($row['result_publish_time'])<strtotime("now")) {
							
							echo('
							<td style="background-image: '.$gradient.'">'.$status.'</td>
							<td>'.$row['score'].'/'.$row['max_pts'].'</td>
							<td style="background-image: '.$gradient.'">'.$percentage.'</td>
							</tr>');
						} else if (strtotime($row['result_publish_time'])>strtotime("now"))
						{
							echo('<td colspan="3"><i class="fa fa-eye-slash"></i>&nbsp;&nbsp;Wynik ukryty</td></tr>');
						} else {
							echo('<td colspan="3" style="background-image: '.$gradient.'">'.$status.'</td></tr>');
						}
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
		<div class="window">
			<h2 class="window_title">Twoje postępy</h2>
			<?php
				$db_query = $pdo->prepare('SELECT AVG(score_percentage) AS avg_score FROM SUBMISSIONS WHERE user_id=:uid');
				$db_query->execute(['uid' => $_SESSION['AUTH_ID']]);
				$avg_score = 0;
				$avg_score = round($db_query->fetch()['avg_score'],1);

				$db_query = $pdo->prepare('SELECT
					DATE(submission_time) as day,
					SUM(score) as daily_points
					FROM SUBMISSIONS
					WHERE user_id = :uid
					AND submission_time >= CURDATE() - INTERVAL 30 DAY
					GROUP BY day
					ORDER BY day ASC;');
				$db_query->execute(['uid' => $_SESSION['AUTH_ID']]);

				$data = $db_query->fetchAll(PDO::FETCH_ASSOC);

				$labels = [];
				$points = [];

				$currentSum = 0;

				$period = new DatePeriod(
					new DateTime("-30 days"),
					new DateInterval("P1D"),
					new DateTime("+0 day")
				);

				$map = [];
				foreach ($data as $row) {
					$map[$row['day']] = $row['daily_points'];
				}

				foreach ($period as $date) {
					$day = $date->format("Y-m-d");

					if (isset($map[$day])) {
						$currentSum += $map[$day];
					}

					$labels[] = $date->format("j M");
					$points[] = $currentSum;
				}
			?>
			<script>
				const dashboard_progress_pts = <?php echo json_encode($points); ?>;
				const dashboard_labels = <?php echo json_encode($labels); ?>;
			</script>
			<div style="display: flex; gap: 3vmax;">
				<div style="height: 13vmax; width: 10vmax; padding: 1vmax; margin-left: 5%; display: flex; flex-direction: column; align-items: center; text-align: center;">
					<div style="position: relative; width: 10vmax; height: 10vmax;">
						<canvas id="dashboard_progress_correct_anwsers"></canvas>
						<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; font-size: 2vmax;">
							<?php echo($avg_score); ?>%
						</div>
					</div>
					<small style="width: 100%; font-size: 0.8vw; margin-top: 2vmax;">Średnia poprawność</small>
				</div>
				<div style="max-height: 12vmax; width: calc(100% - 22vmax); padding: 1vmax; margin: 0; display: flex; flex-direction: column; align-items: center; text-align: center;">
					<canvas id="dashboard_progress_points" style="width: 100%; float: right;"></canvas>
					<br />
					<small style="width: 100%;">Ilość punktów w ciągu ostatnich 30 dni</small>
				</div>
			</div>
			<br />
			<script>
			
				dashboard_progress_pts_chart = new Chart(document.getElementById('dashboard_progress_correct_anwsers'), {
					type: 'doughnut',
					data: {
					labels: ["Odpowiedzi prawidłowe", "Odpowiedzi błędne"],
					datasets: [{
						data: [<?php echo($avg_score); ?>, <?php echo(100-$avg_score); ?>],
						backgroundColor: ['#00d10a', 'gray']
					}]
					},
					options: {
						responsive: true,
						borderWidth: 0,
						cutout: '75%',
						plugins: {
							legend: {
								display: false
							},
							tooltip: {
								enabled: false
							}
						}
					}
				});

				//dashboard_progress_pts = [0, 10, 25, 100, 120, 250, 260, 280, 290, 500, 500, 510, 525, 600, 620, 750, 960, 1280, 1290, 1500, 2000, 2010, 2025, 2100, 2120, 2150, 2160, 2180, 2290, 2300];
				dashboard_progress_pts_chart = new Chart(document.getElementById('dashboard_progress_points'), {
				type: "line",
				data: {
					labels: dashboard_labels,
					datasets: [{ 
						data: dashboard_progress_pts,
						borderColor: "rgb(0, 179, 255)",
						pointRadius: 2,
						//tension: 0.4,
						fill: true,
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: { display: false }
					},
					y: {
						suggestedMin: 0,
						suggestedMax: 100
					}
				}
				});
			</script>
		</div>
		<div class="window">
			<h2 class="window_title">Aktualności</h2>
			<?php
				$db_query = $pdo->prepare('SELECT * FROM ARTICLES ORDER BY id DESC LIMIT 3');
				$db_query->execute();

				$isfound=0;
				while($row = $db_query->fetch())
				{
					$isfound++;
					$article_id = $row['id'];
					$article_title = $row['title'];
					$article_author = $row['author'];
					$article_time = $row['time'];
					$article_content = $row['content'];
					echo('<div class="news">
						<span><i class=\'fas fa-user-circle\'></i>&nbsp;'.$row['author'].'&emsp;<i class=\'fas fa-clock\'></i>&nbsp;'.$row['time'].'</span>
						<h3>'.$row['title'].'</h3>
						<p>'.$row['content'].'</p>
					</div>');
				}
				if($isfound==0)
				{
					echo("<center>Jeszcze tu niczego nie ma!</center><br />");
				}
			?>
			<br />
		</div>
	</div>
	<div class="window" style="max-width: 28%;">
		<?php
			include_plugins_for("dashboard_side_panel");
		?>
		<h3 class="window_title">Proponowane zbiory zadań</h3>
		<div id="dashboard_propositions_bar">
			<?php
				$db_query = $pdo->prepare('SELECT *, USERS.username AS author FROM PROBLEMSETS INNER JOIN USERS ON PROBLEMSETS.author_id=USERS.USER_ID ORDER BY PROBLEMSETS.SET_ID DESC LIMIT 3;');
				$db_query->execute();
				
				$count = 0;
				while($row = $db_query->fetch())
				{
					$count++;
					echo('<a href="?p=set&id='.$row['SET_ID'].'" style="text-decoration: none;"><div class="dashboard_content_set" style="background: linear-gradient(rgba(0, 0, 0, 0.7),rgba(0, 0, 0, 0.7)), url(\''.$row['img_path'].'\'); background-size: cover;">
				<div class="dashboard_content_set_metadata">
					<h3>'.$row['title'].'</h3>
					<small style="top: -1vmax; position: relative;">Autor: '.$row['author'].'</small>
				</div>
			</div></a>');
				}

				if ($count==0) {
					echo("<center>Brak dostępnych zawartości</center>");
				}
			?>
			<br />
			<br />
		</div>
	</div>
</div>
<br />
<br />