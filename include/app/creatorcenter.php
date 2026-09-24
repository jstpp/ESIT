<style>
	.window {
		transition: 0.3s;
	}
	
	.window .window_title {
		margin-left: 5%;
		margin-top: 1.5vw;
	}

	.window:hover {
		background-color: var(--container-hover-bg);
	}

	.solutions_results_block {
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

	.solutions_results_block:hover {
		box-shadow: 0 0 0.1vmax 0.2vmax var(--container-hover-bg);
	}

	.solutions_results_block_progress {
		background-color: var(--container-hover-bg);
		width: 8vmax;
		border-radius: 0.5vmax;
		overflow: hidden;
		display: flex;
		align-items: center;
	}
	.solutions_results_block_progress_bar {
		padding-top: 0.5vmax;
		padding-bottom: 0.5vmax;
		width: calc(8vmax * 0.2);
		height: 100%;
		display: flex;
		align-items: center;
	}
	.solutions_results_block_progress_bar h2 {
		margin-left: 1vmax;
	}
</style>

<div style="display: flex; gap: 1vmax; padding: 1vmax;">
	<div style="flex: 1; display: flex; flex-direction: column; gap: 0.5vmax;">
		<center style="margin-bottom: -0.5vmax;">
			<h2><?php echo(__("Reevaluation requests")); ?></h2>
		</center>
		<?php
			$db_query = $pdo->prepare('SELECT USERS.username AS username, SUBMISSIONS.SUBMISSION_ID AS id, SUBMISSIONS.mode AS mode, SUBMISSIONS.verification_time, SUBMISSIONS.submission_time AS submission_time, SUBMISSIONS.score AS score, SUBMISSIONS.score_percentage AS score_percentage, CONTENT.title AS title, CONTENT.type AS type, CONTENT.maxpoints AS max_pts, CONTENT.CONTENT_ID AS problem_id, CONTENT.result_publish_time AS result_publish_time
									   FROM REEVALUATION_REQUESTS 
									   INNER JOIN SUBMISSIONS ON SUBMISSIONS.SUBMISSION_ID=REEVALUATION_REQUESTS.submission_id
									   INNER JOIN USERS ON SUBMISSIONS.user_id=USERS.USER_ID
									   INNER JOIN CONTENT ON SUBMISSIONS.problem_id=CONTENT.CONTENT_ID');
			$db_query->execute();
			$count = 0;

			while($row = $db_query->fetch())
			{
				$count++;
				if(strtotime($row['result_publish_time'])>strtotime("now"))
				{
					$gradient = "linear-gradient(to left, rgba(173, 170, 171, 0.5) 0%,transparent 50%);";
					$percentage = "...";
					$status = "<i class=\"fa fa-eye-slash\"></i>&nbsp;&nbsp;".__("Result unavailable");
				}
				else if($row['score_percentage']==0)
				{
					$gradient = "linear-gradient(to left, rgba(255, 61, 110, 0.5) 0%,transparent 50%);";
					$percentage = (int)($row['score_percentage']);
					$status = __("Incorrect");
				} else if ($row['score_percentage']==100)
				{
					$gradient = "linear-gradient(to left, rgba(0, 209, 10, 0.5) 0%,transparent 50%);";
					$percentage = (int)($row['score_percentage']);
					$status = __("Fully correct");
				} else if ($row['score_percentage']==-1)
				{
					$gradient = "linear-gradient(to left, rgba(173, 170, 171, 0.5) 0%,transparent 50%);";
					$percentage = "...";
					$status = __("In queue...");
				} else {
					$gradient = "linear-gradient(to left, rgba(142, 237, 40, 0.5) 0%,transparent 50%);";
					$percentage = (int)($row['score_percentage']);
					$status = __("Partially correct");
				}

				switch($row['type'])
				{
					case 1:
						$problem = content_type_identification('alg');
						$resultdest = "algresult";
						break;
					case 2:
						$problem = content_type_identification('ctf');
						$resultdest = "ctfresult";
						break;
					case 3:
						$problem = content_type_identification('och');
						$resultdest = "testresult";
						break;
					case 4:
						$problem = content_type_identification('mch');
						$resultdest = "testresult";
						break;
					case 5:
						$problem = content_type_identification('opn');
						$resultdest = "formresult";
						break;
					default:
						$problem = content_type_identification('unk');
						break;
				}

				echo('<a href="index.php?p='.$resultdest.'&sid='.$row['id'].'" class="solutions_results_block" style="background-image: '.$gradient.';">
					<div style="display: flex; flex-direction: column; flex: 1;">
						<h2 style="margin: 0 0 0.5vmax 0;">'.htmlentities($row['title']).'</h2>
						<small style="margin-bottom: 0.5vmax;">Nadesłane przez: '.htmlentities($row['username']).'</small>
						<small style="font-size: 0.7vmax; background-color: '.$problem['color'].'; width: 8vmax; text-align: center; padding: 0.4vmax; border-radius: 1vmax;"><i class="'.$problem['icon'].'"></i>&nbsp;&nbsp;'.$problem['full_name'].'</small>
					</div>');
				
				if ($row['score_percentage']!=-1 and strtotime($row['result_publish_time'])<strtotime("now")) {	
					echo('	<div class="solutions_results_block_status" style="flex: 1;">'.$status.'</div>
							<div>
								<small>'.$row['submission_time'].'</small><br />
								<div class="solutions_results_block_progress">
									<div class="solutions_results_block_progress_bar" style="width: calc(8vmax * '.floatval($percentage/100).'); background-color: '.$problem['color'].';">
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
			if($count==0)
			{
				echo('
				<div style="margin-left: auto; margin-right: auto; margin-top: 5vmax; text-align: center; display: flex; flex-direction: column; justify-content: cetner; width: 30%; padding: 3vmax; background-color: var(--container-bg); box-shadow: 0 0 0.1vmax 0.2vmax var(--container-hover-bg); border-radius: 1vw;">
					<i class="fa fa-hourglass-3" style="font-size: 7vmax; margin: auto;"></i>
					<center style="margin-top: 2vmax; user-select: none;"><i>'.__("There's nothing here yet!").'</i></center>
				</div>
				');
			}
		?>
	</div>
	<div style="flex: 1;">
		<center>
			<h2><?php echo(__("Forms waiting for evaluation")); ?></h2>
		</center>
		<?php
			$db_query = $pdo->prepare('SELECT CONTENT.title AS title, SUBMISSIONS.content AS content, SUBMISSIONS.SUBMISSION_ID AS subid, USERS.username AS username FROM SUBMISSIONS INNER JOIN USERS ON SUBMISSIONS.user_id=USERS.USER_ID INNER JOIN CONTENT ON SUBMISSIONS.problem_id=CONTENT.CONTENT_ID WHERE SUBMISSIONS.content<>"-" AND SUBMISSIONS.score=-1 ORDER BY SUBMISSIONS.submission_time DESC');
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
				<div style="margin-left: auto; margin-right: auto; margin-top: 5vmax; text-align: center; display: flex; flex-direction: column; justify-content: cetner; width: 30%; padding: 3vmax; background-color: var(--container-bg); box-shadow: 0 0 0.1vmax 0.2vmax var(--container-hover-bg); border-radius: 1vw;">
					<i class="fa fa-hourglass-3" style="font-size: 7vmax; margin: auto;"></i>
					<center style="margin-top: 2vmax; user-select: none;"><i>'.__("There's nothing here yet!").'</i></center>
				</div>
				');
			}
		?>
	</div>
</div>
<br />
<br />