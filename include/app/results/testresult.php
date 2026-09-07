<?php
	if(isset($_GET['sid']))
	{
		if($_SESSION['AUTH_LEVEL']<5)
		{
			$db_query = $pdo->prepare('SELECT DISTINCT *, SUBMISSIONS.comment AS scomment FROM SUBMISSIONS INNER JOIN PROBLEMS ON SUBMISSIONS.problem_id=PROBLEMS.PROBLEM_ID WHERE SUBMISSIONS.SUBMISSION_ID=:sid');
			$db_query->execute(['sid' => $_GET['sid']]);
		} else {
			$db_query = $pdo->prepare('SELECT DISTINCT *, SUBMISSIONS.comment AS scomment FROM SUBMISSIONS INNER JOIN PROBLEMS ON SUBMISSIONS.problem_id=PROBLEMS.PROBLEM_ID WHERE SUBMISSIONS.SUBMISSION_ID=:sid AND SUBMISSIONS.user_id=:uid');
			$db_query->execute(['sid' => $_GET['sid'], 'uid' => $_SESSION['AUTH_ID']]);
		}

		$row = $db_query->fetch();
		$ident = ($row['type']==4) ? problem_type_identification('mch') : problem_type_identification('och');
		$questions = array();

		$db_query = $pdo->prepare('SELECT * FROM TEST_QUESTIONS WHERE problem_id=:pid');
		$db_query->execute(['pid' => $row['PROBLEM_ID']]);

		while($x = $db_query->fetch())
		{
			array_push($questions, $x);
		}

		if(!isset($row['score_percentage']))
		{
			kick();
		}

		if(strtotime($row['result_publish_time'])<strtotime("now"))
		{
			if($row['score_percentage']==0)
			{
				$gradient = "linear-gradient(to left,#ff3d6e 0%,transparent 50%);";
				$percentage = $row['score_percentage']."%";
				$status = __("Incorrect");
			} else if ($row['score_percentage']==100)
			{
				$gradient = "linear-gradient(to left,#00d10a 0%,transparent 50%);";
				$percentage = $row['score_percentage']."%";
				$status = __("Fully correct");
			} else if ($row['score_percentage']==-1)
			{
				$gradient = "linear-gradient(to left,gray 0%,transparent 50%);";
				$percentage = "...";
				$status = __("In queue...");
			} else {
				$gradient = "linear-gradient(to left,#8eed28 0%,transparent 50%);";
				$percentage = $row['score_percentage']."%";
				$status = __("Partially correct");
			}
		} else {
			$gradient = "linear-gradient(to left,gray 0%,transparent 50%);";
			$percentage = "...";
			$status = __("Result unavailable");
		}

	} else {
		kick();
	}
?>
<style>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<center>
	<h1><?php echo(__("Evaluation results")); ?></h1>
</center>
<div class="window">
	<h2 class="window_title"><a style="color: var(--text); padding: 0.5vmax 1vmax; border-radius: 0.5vmax; background-color: <?php echo($ident['color']); ?>" href="?p=problem&id=<?php echo($row['PROBLEM_ID']); ?>"><?php echo($row['title']); ?></a>&emsp;(#<?php echo($row['PROBLEM_ID']); ?>)</h2>
	<br />
	<div style="margin-left: 5%; width: 90%;">
		<div style="display: flex; gap: 2vmax;">
			<div id="charts" style="padding: 2vmax; background-color: var(--container-hover-bg); width: fit-content; border-radius: 1vmax;">
				<div id="genv" style="width: 240px; height: 240px; position: relative;">
					<canvas id="gen1"></canvas>
					<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; font-size: 2vmax;">
						<?php echo($percentage); ?>
					</div>
				</div>
			</div>

			<div style="padding: 2vmax; background-image: <?php echo($gradient); ?>; background-color: var(--container-hover-bg); width: auto; border-radius: 1vmax; flex-grow: 5;">
				<?php
					if ($row['score_percentage']!=-1 and strtotime($row['result_publish_time'])<strtotime("now"))
					{
						echo("<div style=\"width: fit-content; padding: 0.5vmax 1vmax; border-radius: 0.5vmax; background-color: ".$ident['color']."\"><b><i class='".$ident['icon']."'></i>&nbsp;&nbsp;".$ident['full_name']."</b></div>");
						echo("<b><br />ID: #".$row['SUBMISSION_ID']."</b>");
						echo("<p>");
						echo(__("Status").":&emsp;<code>".htmlentities($status)."</code><br />");
						echo(__("Submission timestamp").":&emsp;<code>".htmlentities($row['submission_time'])."</code><br />");
						echo(__("Verification timestamp").":&emsp;<code>".htmlentities($row['verification_time'])."</code>");

						echo("<h4>".__("Total").":&emsp;<code>".htmlentities($row['score'])."/".htmlentities($row['maxpoints'])."</code></h4>");
					} else {
						echo('<br /><center><i class="fa fa-cog fa-spin"></i>&nbsp;&nbsp;'.__("Your solution is waiting to be reviewed or its result has been temporarily hidden. The results will be available soon.").'</center>');
					}
				?>
				<br />
			</div>
		</div>
    
		<script>
		  const ctx = document.getElementById('gen1');
    
		  new Chart(ctx, {
			type: 'doughnut',
			data: {
			  labels: ["<?php echo(__("Correct anwsers")); ?>", "<?php echo(__("Incorrect anwsers")); ?>"],
			  datasets: [{
				data: [<?php echo((int)$percentage); ?>, <?php echo(100-(int)$percentage); ?>],
				backgroundColor: ['#00d10a', '#ff3d6e'],
				weight: [1],
			  }]
			},
			options: {
				responsive: true,
				borderWidth: 0,
				cutout: 70,
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
		</script>
	</div>
	<br />
	<br />
</div>
<br />
<?php
	if ($row['score_percentage']==-1 or strtotime($row['result_publish_time'])>strtotime("now"))
	{
		echo("<script>document.getElementById('charts').style.display = 'none';</script>");
	}
?>
<br />