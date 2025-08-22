<?php
	$db_query = $pdo->prepare('SELECT * FROM PROBLEMSETS WHERE SET_ID=:setid');
	$db_query->execute(['setid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);
	$isfound = 0;

	while($row = $db_query->fetch())
	{
		$isfound++;
		$settitle = $row['title'];
		$setdescription = $row['description'];
	}

	if($isfound!=1) 
	{ 
		kick();
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
	}

	.window .window_title {
		margin-left: 5%;
		margin-top: 1.5vw;
	}

	.window table {
		width: 90%;
		margin-left: 5%;
		user-select: none;
	}
	.window table a {
		text-decoration: none;
		color: rgb(0, 179, 255);
	}
	.window table td {
		border-top: 0.1vw solid gray;
		padding: 0.5vw 0.5vw;
	}
	.window table td a {
		font-weight: bold;
		text-align: center;
		transition: 0.3s;
		cursor: pointer;
	}
	.window table tr {
		transition: 0.2s;
		cursor: default;
	}
	.window table tr:hover {
		background-color: var(--container-hover-bg);
	}
	.window #questlist tr {
		cursor: pointer;
	}
	.window .forminput {
		border: 0; 
		padding: 0.75vw 1.25vw; 
		color: #dae2e6; 
		background-color: #2a2c2e; 
		font-family: inherit;
		cursor: pointer;
		transition: 0.4s;
		text-decoration: none;
	}
	.window .forminput:hover {
		background-color: #3e4145;
	}
</style>
<br />
<br />
<center>
	<h1><?php echo(htmlentities($settitle)); ?></h1>
</center>
<br />
<br />
<div class="window">
	<h2 class="window_title" style="margin-left: 2.5%;">Informacje</h2>
	<p style="margin-left: 2.5%;"><?php echo(htmlentities($setdescription)); ?></p>
	<br />
</div>
<div class="window" style="width: 48%; float: left;">
	<h2 class="window_title" style="float: left;">Dostępne zadania</h2>
	<?php if(has_a_priority(3)) echo('<a href="?p=addproblem&sid='.filter_var($_GET['id'], FILTER_VALIDATE_INT).'" class="forminput" style="float: right; margin-right: 5%; margin-top: 2.5%;">Dodaj zadanie</a>'); ?>
	<table id="questlist">
		<?php
			if(isset($_GET['id']))
			{
				try {
					$db_query = $pdo->prepare('SELECT * FROM PROBLEMS WHERE problemset=:setid AND publish_time<:currenttime');
					$db_query->execute(['setid' => filter_var($_GET['id'], FILTER_VALIDATE_INT), 'currenttime' => date("Y-m-d H:i:s", strtotime("now"))]);

					while($row = $db_query->fetch())
					{
						if($row['type']==1)
						{
							$problemtype = "<i class='fas fa-cloud'></i>&nbsp;Algorytmiczne";
						} else if($row['type']==2)
						{
							$problemtype = "<i class='fas fa-flag'></i></i>&nbsp;&nbsp;Capture The Flag";
						} else if($row['type']==3)
						{
							$problemtype = "<i class='fas fa-check-square'></i>&nbsp;&nbsp;Jednokrotnego wyboru";
						} else if($row['type']==4)
						{
							$problemtype = "<i class='fas fa-check-double'></i>&nbsp;&nbsp;Wielokrotnego wyboru";
						} else if($row['type']==5)
						{
							$problemtype = "<i class='fa fa-edit'></i>&nbsp;&nbsp;Formularz";
						} else {
							$problemtype = "Nieznany typ";
						}

						$cdb_query = $pdo->prepare('SELECT COUNT(*) AS attempts, MAX(SCORE) AS maxscore FROM SUBMISSIONS WHERE problem_id=:pid AND user_id=:uid');
						$cdb_query->execute(['pid' => $row['PROBLEM_ID'], 'uid' => $_SESSION['AUTH_ID']]);

						$content = $cdb_query->fetch();
						$count = $content['attempts'];
						if((int)$content['maxscore']!=-1)
						{
							$maxscore = $content['maxscore'];
						} else {
							$maxscore = 0;
						}

						if(!isset($maxscore)) { $maxscore=0; }

						if($row['maxattempts']-$count==1)
						{
							$attemptsgradient = "linear-gradient(to left,#ffc117 0%,transparent 50%)";
							$attempts = "Ostatnia próba";
						} else if ($row['maxattempts']-$count<1)
						{
							$attemptsgradient = "linear-gradient(to left,#ff3d6e 0%,transparent 50%)";
							$attempts = "Brak prób";
						} else {
							$attemptsgradient = "linear-gradient(to left,rgb(0, 179, 255) 0%,transparent 50%)";
							$attempts = ($row['maxattempts']-$count)." prób";
						}

						echo('<tr onClick="window.location.href = \'?p=quest&id='.$row['PROBLEM_ID'].'\';">
							<td>#'.$row['PROBLEM_ID'].'</td>
							<td><a href="?p=quest&id='.$row['PROBLEM_ID'].'">'.$row['title'].'</a></td>
							<td>'.$problemtype.'</td>');
						if(strtotime($row['result_publish_time'])<strtotime("now"))
						{
							echo('
								<td>'.$maxscore.'/'.$row['maxpoints'].' pkt.</td>
								<td style="background-image: '.$attemptsgradient.';">'.$attempts.'</td>
							</tr>');
						} else {
							echo('
								<td style="text-align: center;"><i class="fa fa-eye-slash"></i></td>
								<td style="background-image: '.$attemptsgradient.';">'.$attempts.'</td>
							</tr>');
						}
					}

				} catch (Exception $e)
				{
					echo("<meta http-equiv='refresh' content='0; url=".$error_link."' />");
					die;
				}
			} else {
				echo("<meta http-equiv='refresh' content='0; url=".$error_link."' />");
				die;
			}
		?>
	</table>
	<br />
	<br />
</div>
<div class="window" style="width: 44%; float: left;">
	<h2 class="window_title">Tablica wyników</h2>
	<div style="overflow: auto; width: 90%; margin-left: 5%;">
		<table id="scoreboard">
			<tr>
				<th>Użytkownik</th>
				<th>Suma</th>
				<?php 
					$problem_array = array();
					$scores = array();
					$temp_table_user_ids = array();
					$temp_table_total_scores = array();
					$db_query = $pdo->prepare('SELECT * FROM PROBLEMS WHERE problemset=:setid AND result_publish_time<:currenttime ORDER BY PROBLEM_ID DESC');
					$db_query->execute(['setid' => filter_var($_GET['id'], FILTER_VALIDATE_INT), 'currenttime' => date("Y-m-d H:i:s", strtotime("now"))]);

					while($row = $db_query->fetch())
					{
						echo('<th>#'.$row['PROBLEM_ID'].'</th>');
						array_push($problem_array, $row['PROBLEM_ID']);
					}
					
					echo("</tr>");

					foreach($problem_array as $p)
					{
						$db_query = $pdo->prepare('SELECT SUBMISSIONS.user_id AS user_id, SUBMISSIONS.score AS score, SUBMISSIONS.score_percentage AS score_percentage, SUBMISSIONS.problem_id AS problem_id, USERS.username AS username, PROBLEMS.result_publish_time AS result_publish_time FROM SUBMISSIONS INNER JOIN USERS ON SUBMISSIONS.user_id=USERS.USER_ID INNER JOIN PROBLEMS ON SUBMISSIONS.problem_id=PROBLEMS.PROBLEM_ID WHERE SUBMISSIONS.problem_id=:pids AND SUBMISSIONS.problemset_id=:psids AND PROBLEMS.result_publish_time<:currenttime ORDER BY SUBMISSIONS.problem_id DESC');
						$db_query->execute(['pids' => $p, 'psids' => filter_var($_GET['id'], FILTER_VALIDATE_INT), 'currenttime' => date("Y-m-d H:i:s", strtotime("now"))]);

						while($row = $db_query->fetch())
						{
							if(!isset($scores[$row['user_id']]['user_data']['username']))
							{
								$scores[$row['user_id']]['user_data']['username'] = $row['username'];
								$scores[$row['user_id']]['user_data']['user_id'] = $row['user_id'];
								$scores[$row['user_id']]['user_data']['total_score'] = 0;
							}

							if(!isset($scores[$row['user_id']][$row['problem_id']]['score']))
							{
								if($row['score']!=-1)
								{
									$scores[$row['user_id']][$row['problem_id']]['score'] = $row['score'];
									$scores[$row['user_id']][$row['problem_id']]['percentage'] = $row['score_percentage'];
									$scores[$row['user_id']]['user_data']['total_score'] += $row['score'];
								}
							} else {
								if($scores[$row['user_id']][$row['problem_id']]['score']<$row['score'])
								{
									if($row['score']!=-1)
									{	
										$scores[$row['user_id']]['user_data']['total_score'] += ($row['score'] - $scores[$row['user_id']][$row['problem_id']]['score']);
									}
									$scores[$row['user_id']][$row['problem_id']]['score'] = $row['score'];
									$scores[$row['user_id']][$row['problem_id']]['percentage'] = $row['score_percentage'];
								}
							}
						}
					}

					foreach(array_keys($scores) as $u) #iterate for each user
					{
						array_push($temp_table_total_scores, $scores[$u]['user_data']['total_score']);
						array_push($temp_table_user_ids, $scores[$u]['user_data']['user_id']);
					}


					array_multisort($temp_table_total_scores, SORT_DESC, $temp_table_user_ids);

					foreach($temp_table_user_ids as $u) #iterate for each user
					{
						if($_SESSION['AUTH_ID']==$u)
						{
							echo('<tr style="background-color: rgb(0, 179, 255); font-weight: bold;">');
						} else {
							echo('<tr>');
						}
						echo('<td>'.$scores[$u]['user_data']['username'].'</td>');
						echo('<td>'.$scores[$u]['user_data']['total_score'].'</td>');
						foreach($problem_array as $p)
						{
							if(isset($scores[$u][$p]['score']) and (int)$scores[$u][$p]['score']!=-1)
							{
								echo('<td>'.$scores[$u][$p]['score'].'</td>');
							} else {
								echo('<td>0</td>');
							}
						}
						echo('</tr>');
					}
				?>
		</table>
	</div>
	<br />
	<br />
</div>
<br style="clear: both;" />
<br />
<br />