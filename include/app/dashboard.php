<style>
	#results {
		display: flex;
		flex-direction: column;
		gap: 0.5vmax;
	}

	.dashboard_results_block {
		padding: 1vmax;
		background-color: var(--container-hover-bg);
		border-radius: 1vmax;
		width: calc(90% - 2vmax);
		margin-left: 5%;

		text-decoration: none;
		color: var(--text) !important;

		display: flex;
		gap: 1vmax;
		justify-content: stretch;
		align-items: center;
		transition: 0.3s;
		cursor: pointer;
	}

	.dashboard_results_block:hover {
		box-shadow: 0 0 0.1vmax 0.2vmax var(--container-hover-bg-textbox);
	}

	.dashboard_results_block_progress {
		background-color: var(--container-hover-bg);
		width: 8vmax;
		border-radius: 0.5vmax;
		overflow: hidden;
		display: flex;
		align-items: center;
	}
	.dashboard_results_block_progress_bar {
		padding-top: 0.5vmax;
		padding-bottom: 0.5vmax;
		width: calc(8vmax * 0.2);
		height: 100%;
		display: flex;
		align-items: center;
	}
	.dashboard_results_block_progress_bar h2 {
		margin-left: 1vmax;
	}
	
	.window .news {
		background-color: var(--container-hover-bg);
		width: 85%;
		margin-left: 5%;
		margin-top: 0.5vw;
		padding: 1% 2%;
		border-radius: 1vmax;
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
		gap: 1vmax;
		justify-content: center;
		flex-direction: column;
		margin-bottom: -1vmax;
	}

	.dashboard_content_set {
		width: 97%;
		transition: 0.3s;
		min-height: 13vmax;
		box-shadow: 0 0 0.1vmax 0.2vmax var(--container-hover-bg);
		border-radius: 0.5vw;
		background-color: var(--bg);
		overflow: hidden;
	}

	.dashboard_content_set:hover {
		box-shadow: 0 0 0.1vmax 0.2vmax rgb(0, 179, 255);
		cursor: pointer;
	}

	.dashboard_content_set:hover > div > * {
		transition: 0.3s;
	}

	.dashboard_content_set:hover > div > h3 {
		font-size: 1.5vmax;
	}

	.dashboard_content_set:hover > div > small {
		font-size: 1vmax;
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
<div style="display: flex;">
	<div id="dashboard_main" style="min-width: 72%; display: flex; flex-direction: column; align-items: stretch;">
		<div class="window">
			<h2 class="window_title">Twoje postępy</h2>
			<?php
				$db_query = $pdo->prepare('SELECT AVG(score_percentage) AS avg_score FROM SUBMISSIONS WHERE user_id=:uid');
				$db_query->execute(['uid' => $_SESSION['AUTH_ID']]);

				$avg_score = 0;
				while($row = $db_query->fetch()) {
					$avg_score = isset($row['avg_score']) ? round($row['avg_score'], 1) : 0;
				}

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
						backgroundColor: ['rgb(0, 179, 255)', 'rgba(21, 33, 46, 1)']
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

				dashboard_progress_pts_chart = new Chart(document.getElementById('dashboard_progress_points'), {
				type: "line",
				data: {
					labels: dashboard_labels,
					datasets: [{ 
						data: dashboard_progress_pts,
						borderColor: "rgb(0, 179, 255)",
						pointRadius: 2,
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
			<h2 class="window_title">Moje ostatnie rozwiązania</h2>
			<div id="results">
				<?php
					$db_query = $pdo->prepare('SELECT SUBMISSIONS.SUBMISSION_ID AS id, SUBMISSIONS.mode AS mode, SUBMISSIONS.verification_time, SUBMISSIONS.submission_time AS submission_time, SUBMISSIONS.score AS score, SUBMISSIONS.score_percentage AS score_percentage, PROBLEMS.title AS title, PROBLEMS.type AS type, PROBLEMS.maxpoints AS max_pts, PROBLEMS.PROBLEM_ID AS problem_id, PROBLEMS.result_publish_time AS result_publish_time FROM SUBMISSIONS INNER JOIN PROBLEMS ON SUBMISSIONS.problem_id=PROBLEMS.PROBLEM_ID WHERE SUBMISSIONS.user_id=:uid ORDER BY SUBMISSIONS.submission_time DESC LIMIT 4');
					$db_query->execute(['uid' => $_SESSION['AUTH_ID']]);

					$isfound = 0;
					while($row = $db_query->fetch())
					{
						$isfound++;
						if(strtotime($row['result_publish_time'])>strtotime("now"))
						{
							$gradient = "linear-gradient(to left, rgba(173, 170, 171, 0.5) 0%,transparent 50%);";
							$percentage = "...";
							$status = "<i class=\"fa fa-eye-slash\"></i>&nbsp;&nbsp;Wynik ukryty";
						}
						else if($row['score_percentage']==0)
						{
							$gradient = "linear-gradient(to left, rgba(255, 61, 110, 0.5) 0%,transparent 50%);";
							$percentage = (int)($row['score_percentage']);
							$status = "Całkowicie niepoprawne";
						} else if ($row['score_percentage']==100)
						{
							$gradient = "linear-gradient(to left, rgba(0, 209, 10, 0.5) 0%,transparent 50%);";
							$percentage = (int)($row['score_percentage']);
							$status = "Bez błędów";
						} else if ($row['score_percentage']==-1)
						{
							$gradient = "linear-gradient(to left, rgba(173, 170, 171, 0.5) 0%,transparent 50%);";
							$percentage = "...";
							$status = "W kolejce";
						} else {
							$gradient = "linear-gradient(to left, rgba(142, 237, 40, 0.5) 0%,transparent 50%);";
							$percentage = (int)($row['score_percentage']);
							$status = "Częściowo poprawne";
						}

						switch($row['type'])
						{
							case 1:
								$problem = problem_type_identification('alg');
								$resultdest = "algresult";
								break;
							case 2:
								$problem = problem_type_identification('ctf');
								$resultdest = "ctfresult";
								break;
							case 3:
								$problem = problem_type_identification('och');
								$resultdest = "testresult";
								break;
							case 4:
								$problem = problem_type_identification('mch');
								$resultdest = "testresult";
								break;
							case 5:
								$problem = problem_type_identification('opn');
								$resultdest = "formresult";
								break;
							default:
								$problem = problem_type_identification('unk');
								break;
						}

						echo('<a href="index.php?p=ctfresult&sid='.$row['id'].'" class="dashboard_results_block" style="background-image: '.$gradient.';">
							<div style="display: flex; flex-direction: column; flex: 1;">
								<h2 style="margin: 0 0 0.5vmax 0;">'.htmlentities($row['title']).'</h2>
								<small style="font-size: 0.7vmax; background-color: '.$problem['color'].'; width: 8vmax; text-align: center; padding: 0.4vmax; border-radius: 1vmax;"><i class="'.$problem['icon'].'"></i>&nbsp;&nbsp;'.$problem['full_name'].'</small>
							</div>');
						
						if ($row['score_percentage']!=-1 and strtotime($row['result_publish_time'])<strtotime("now")) {	
							echo('	<div class="dashboard_results_block_status" style="flex: 1;">'.$status.'</div>
									<div>
										<small>'.$row['submission_time'].'</small><br />
										<div class="dashboard_results_block_progress">
											<div class="dashboard_results_block_progress_bar" style="width: calc(8vmax * '.floatval($percentage/100).'); background-color: '.$problem['color'].';">
												<h2 style="margin-top: 0; margin-bottom: 0;">'.$percentage.'%</h2>
											</div>
										</div>
									</div>');
						} else {
							echo('<div>
										'.$status.'
									</div>');
						}
						echo('</a>');
					}

					if($isfound==0)
					{
						echo("<center>Jeszcze tu niczego nie ma!</center>");
					}
				?>
			</div>
			<br />
			<br />
		</div>
		<div class="window" style="flex: 1;">
			<h2 class="window_title">Aktualności</h2>
			<?php
				$db_query = $pdo->prepare('SELECT * FROM ARTICLES ORDER BY id DESC LIMIT 3');
				$db_query->execute();

				$news_count=0;
				while($row = $db_query->fetch())
				{
					$news_count++;
					$article_id = $row['id'];
					$article_title = $row['title'];
					$article_author = $row['author'];
					$article_time = $row['time'];
					$article_content = $row['content'];
					echo('<div class="news">
						<span><i class=\'fas fa-user-circle\'></i>&nbsp;'.htmlentities($row['author']).'&emsp;<i class=\'fas fa-clock\'></i>&nbsp;'.htmlentities($row['time']).'</span>
						<h3>'.htmlentities($row['title']).'</h3>
						<p>'.$row['content'].'</p>
					</div>');
				}
				if($news_count==0)
				{
					echo("<center>Jeszcze tu niczego nie ma!</center><br />");
				}
			?>
			<br />
		</div>
	</div>
	<div id="proposed_channels" style="min-width: 27.5%; max-width: 30%; margin-left: 0; margin-right: 1%; display: flex; flex-direction: column; align-items: stretch;">
		<?php
			include_plugins_for("dashboard_side_panel");
		?>
		<h3 class="window_title"><i class='fas fa-lightbulb'></i>&emsp;Proponowane</h3>
		<div id="dashboard_propositions_bar">
			<?php
				$db_query = $pdo->prepare('SELECT *, USERS.username AS author FROM CHANNELS INNER JOIN USERS ON CHANNELS.author_id=USERS.USER_ID ORDER BY CHANNELS.CHANNEL_ID DESC LIMIT :limit;');
				$db_query->execute(['limit' => (4 + $news_count)]);
				
				$count = 0;
				while($row = $db_query->fetch())
				{
					$count++;
					echo('<a href="?p=channel&id='.$row['CHANNEL_ID'].'" style="flex: 1; text-decoration: none;"><div class="dashboard_content_set" style="background: linear-gradient(rgba(0, 0, 0, 0.7),rgba(0, 0, 0, 0.7)), url(\''.$row['img_path'].'\'); background-size: cover;">
				<div class="dashboard_content_set_metadata">
					<h3>'.htmlentities($row['title']).'</h3>
					<small style="top: -1vmax; position: relative;">Autor: '.htmlentities($row['author']).'</small>
				</div>
			</div></a>');
				}

				if ($count==0) {
					echo("<script>document.getElementById('proposed_channels').style.display = 'none';</script>");
					echo("<script>document.getElementById('dashboard_main').style.width = '100%';</script>");
				}
			?>
			<br />
			<br />
		</div>
		<br />
	</div>
</div>
<br />
<br />