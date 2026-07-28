<style>
	#results {
		display: flex;
		flex-direction: column;
		gap: 1vmax;
	}
	.mysolutions_results_block {
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

	.mysolutions_results_block:hover {
		box-shadow: 0 0 0.1vmax 0.2vmax var(--container-hover-bg-textbox);
	}

	.mysolutions_results_block_progress {
		background-color: var(--container-hover-bg);
		width: 8vmax;
		border-radius: 0.5vmax;
		overflow: hidden;
		display: flex;
		align-items: center;
	}
	.mysolutions_results_block_progress_bar {
		padding-top: 0.5vmax;
		padding-bottom: 0.5vmax;
		width: calc(8vmax * 0.2);
		height: 100%;
		display: flex;
		align-items: center;
	}
	.mysolutions_results_block_progress_bar h2 {
		margin-left: 1vmax;
	}
</style>

<center>
	<h1>Moje rozwiązania</h1>
</center>
<?php
	include_plugins_for("my_solutions");
?>
<div class="window">
	<h2 class="window_title">Moje rozwiązania</h2>
	<div id="results">
		<?php
			$db_query = $pdo->prepare('SELECT SUBMISSIONS.SUBMISSION_ID AS id, SUBMISSIONS.mode AS mode, SUBMISSIONS.verification_time, SUBMISSIONS.submission_time AS submission_time, SUBMISSIONS.score AS score, SUBMISSIONS.score_percentage AS score_percentage, PROBLEMS.title AS title, PROBLEMS.type AS type, PROBLEMS.maxpoints AS max_pts, PROBLEMS.PROBLEM_ID AS problem_id, PROBLEMS.result_publish_time AS result_publish_time FROM SUBMISSIONS INNER JOIN PROBLEMS ON SUBMISSIONS.problem_id=PROBLEMS.PROBLEM_ID WHERE SUBMISSIONS.user_id=:uid ORDER BY SUBMISSIONS.submission_time DESC');
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

				echo('<a href="index.php?p=ctfresult&sid='.$row['id'].'" class="mysolutions_results_block" style="background-image: '.$gradient.';">
					<div style="display: flex; flex-direction: column; flex: 1;">
						<h2 style="margin: 0 0 0.5vmax 0;">'.htmlentities($row['title']).'</h2>
						<small style="font-size: 0.7vmax; background-color: '.$problem['color'].'; width: 8vmax; text-align: center; padding: 0.4vmax; border-radius: 1vmax;"><i class="'.$problem['icon'].'"></i>&nbsp;&nbsp;'.$problem['full_name'].'</small>
					</div>');
				
				if ($row['score_percentage']!=-1 and strtotime($row['result_publish_time'])<strtotime("now")) {	
					echo('	<div class="mysolutions_results_block_status" style="flex: 1;">'.$status.'</div>
							<div>
								<small>'.$row['submission_time'].'</small><br />
								<div class="mysolutions_results_block_progress">
									<div class="mysolutions_results_block_progress_bar" style="width: calc(8vmax * '.floatval($percentage/100).'); background-color: '.$problem['color'].';">
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
<br />
<br />