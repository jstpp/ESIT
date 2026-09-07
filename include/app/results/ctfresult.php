<?php
	$ident = problem_type_identification('ctf');
	if($_SESSION['AUTH_LEVEL']<5)
	{
		$db_query = $pdo->prepare('SELECT DISTINCT * FROM SUBMISSIONS INNER JOIN PROBLEMS ON SUBMISSIONS.problem_id=PROBLEMS.PROBLEM_ID WHERE SUBMISSIONS.SUBMISSION_ID=:sid');
		$db_query->execute(['sid' => $_GET['sid']]);
	} else {
		$db_query = $pdo->prepare('SELECT DISTINCT * FROM SUBMISSIONS INNER JOIN PROBLEMS ON SUBMISSIONS.problem_id=PROBLEMS.PROBLEM_ID WHERE SUBMISSIONS.SUBMISSION_ID=:sid AND SUBMISSIONS.user_id=:uid');
		$db_query->execute(['sid' => $_GET['sid'], 'uid' => $_SESSION['AUTH_ID']]);
	}

	$row = $db_query->fetch();

	if(strtotime($row['result_publish_time'])<strtotime("now"))
	{
		if($row['score']=='0')
		{
			$comment = __("You were unable to submit a valid flag. Please keep searching!");
			$gradient = "linear-gradient(to left,#ff3d6e 0%,transparent 5%);";
		} else {
			$comment = __("You successfully submitted the correct flag. We're happy for you!");
			$gradient = "linear-gradient(to left,#00d10a 0%,transparent 5%);";
		}
	} else {
		$comment = __("The verification result for your flag is not available yet.");
		$gradient = "linear-gradient(to left, gray 0%,transparent 5%);";
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

<center>
	<h1><?php echo(__("CTF flag evaluation results")); ?></h1>
</center>
<div class="window">
	<h2 class="window_title"><a style="color: var(--text); padding: 0.5vmax 1vmax; border-radius: 0.5vmax; background-color: <?php echo($ident['color']); ?>" href="?p=problem&id=<?php echo($row['PROBLEM_ID']); ?>"><?php echo($row['title']); ?></a>&emsp;(#<?php echo($row['PROBLEM_ID']); ?>)</h2>
	<div style="width: 85%; margin-left: 5%; padding: 2vmax; background-image: <?php echo($gradient); ?>; background-color: var(--container-hover-bg); border-radius: 1vmax;">
		<?php
			if ($row['score_percentage']!=-1 and strtotime($row['result_publish_time'])<strtotime("now"))
			{
				echo("<div style=\"width: fit-content; padding: 0.5vmax 1vmax; border-radius: 0.5vmax; background-color: ".$ident['color']."\"><b><i class='".$ident['icon']."'></i>&nbsp;&nbsp;".$ident['full_name']."</b></div>");
				echo("<b><br />ID: #".$row['SUBMISSION_ID']."</b>");
				echo("<p><i class='fas fa-info-circle'></i>&nbsp;&nbsp;".$comment."</p>");
				echo("<p>");
				echo(__("Submission timestamp").":&emsp;<code>".htmlentities($row['submission_time'])."</code><br />");
				echo(__("Verification timestamp").":&emsp;<code>".htmlentities($row['verification_time'])."</code>");

				echo("<h4>".__("Total").":&emsp;<code>".htmlentities($row['score'])."/".htmlentities($row['maxpoints'])."</code></h4>");
			} else {
				echo('<br /><center><i class="fa fa-cog fa-spin"></i>&nbsp;&nbsp;'.__("Your solution is waiting to be reviewed or its result has been temporarily hidden. The results will be available soon.").'</center>');
			}
		?>
		<br />
	</div>
	<br style="clear: both;" />
	<br />
	<br />
</div>
<br />
<br />