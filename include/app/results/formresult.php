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
		} else {
			$gradient = "linear-gradient(to left,gray 0%,transparent 50%);";
			$percentage = "...";
			$status = "Wynik ukryty";
		}

	} else {
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
		transition: 0.2s;
		user-select: none;
	}

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
	.results code {
		background-color: #dae2e6;
		color: #2a2c2e;
		padding: 0.2vw 0.2vw;
	}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
	MathJax = {
	tex: {
		inlineMath: [['$', '$']]
	}
	};
</script>
<script id="MathJax-script" async
  src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js">
</script>

<center>
	<h1>Raport ze sprawdzania</h1>
</center>
<div class="window">
	<h2 class="window_title"><?php echo($row['title']); ?> (#<?php echo($row['PROBLEM_ID']); ?>)</h2>
	<br />
	<div id="charts" style="margin-left: 5%; width: 90%;">
		<center>
			<div id="genv" style="width: 240px; height: 240px;">
				<canvas id="gen1"></canvas>
			</div>
		</center>
    
		<script>
		  const ctx = document.getElementById('gen1');
    
		  new Chart(ctx, {
			type: 'doughnut',
			data: {
			  labels: ["Odpowiedzi prawidłowe", "Odpowiedzi błędne"],
			  datasets: [{
				data: [<?php echo((int)$percentage); ?>, <?php echo(100-(int)$percentage); ?>],
				backgroundColor: ['#00d10a', '#ff3d6e'],
				weight: [1],
			  }]
			},
			options: {
				responsive: true,
				borderWidth: 0,
				cutout: 98,
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
	<?php
		if ($row['score_percentage']==-1 or strtotime($row['result_publish_time'])>strtotime("now"))
		{
			echo('<center><i class="fa fa-cog fa-spin"></i>&nbsp;&nbsp;Twoje rozwiązanie czeka na sprawdzenie lub jego wynik jest tymczasowo ukryty. Wyniki będą dostępne po pewnym czasie.</center>');
		}
	?>
	<table class="results" id="summary" style="width: 90%; margin-right: 5%; margin-top: 4vmax;">
		<tr>
			<td><?php echo($row['submission_time']); ?></td>
			<td><?php echo($row['verification_time']); ?></td>
			<td><i class='fa fa-edit'></i>&nbsp;Formularz</td>
			<td style="background-image: <?php echo($gradient); ?>"><?php echo($status); ?></td>
			<td><?php if(strtotime($row['result_publish_time'])<strtotime("now")) { echo($row['score']); } else { echo("???"); }?>/<?php echo($row['maxpoints']); ?></td>
			<td style="background-image: <?php echo($gradient); ?>"><?php if(strtotime($row['result_publish_time'])<strtotime("now")) { echo($percentage); } else { echo("???"); } ?></td>
		</tr>
	</table>
	<br style="clear: both;" />
	<br />
	<br />
</div>
<div class="window">
	<h3 class="window_title">Twoja odpowiedź</h3>
	<br />
	<div style="user-select: text; margin-left: 5%; width: 88%; padding: 1% 1%; background-color: #dae2e6; color: #2a2c2e;">
		<?php
			echo($row['content']);
		?>
	</div>
	<br />
	<?php
		if($row['scomment']!="-" and $row['scomment']!="" and strtotime($row['result_publish_time'])<strtotime("now"))
		{
			echo('<p style="margin-left: 5%;"><b>Komentarz sprawdzającego: </b>'.$row['scomment'].'</p>');
		}
	?>
	<br />
</div>
<?php
	if ($row['score_percentage']==-1 or strtotime($row['result_publish_time'])>strtotime("now"))
	{
		echo("<script>document.getElementById('details').style.display = 'none';</script>");
		echo("<script>document.getElementById('charts').style.display = 'none';</script>");
		echo("<script>document.getElementById('summary').style.display = 'none';</script>");
	}
?>
<br />
<br />