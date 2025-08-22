<?php
	if(isset($_GET['sid']))
	{
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

		$results = array();
		$db_query = $pdo->prepare('SELECT * FROM RESULTS INNER JOIN ALG_TEST_LIST ON RESULTS.test_id=ALG_TEST_LIST.TEST_ID WHERE submission_id=:sid');
		$db_query->execute(['sid' => $_GET['sid']]);

		$anws_correct = 0; #Correct anwsers
		$anws_wrong = 0; #Wrong anwsers
		$anws_resource = 0; #Out of time or out of memory

		while($xr = $db_query->fetch())
		{
			$anws_correct += $xr['anws_correct'];
			$anws_wrong += $xr['anws_wrong'];
			$anws_resource += $xr['anws_resource'];
			array_push($results, $xr);
		}
	} else {
		echo("<meta http-equiv='refresh' content='0; url=".$error_link."' />"); 
		die;
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
			  labels: ["Odpowiedzi prawidłowe", "Przekroczono limit", "Odpowiedzi błędne"],
			  datasets: [{
				data: [<?php echo($anws_correct); ?>, <?php echo($anws_resource); ?>, <?php echo($anws_wrong); ?>],
				backgroundColor: ['#00d10a', '#ffc117', '#ff3d6e'],
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
			echo('<center><i class="fa fa-cog fa-spin"></i>&nbsp;&nbsp;Twoje rozwiązanie czeka na sprawdzenie lub jego wynik został tymczasowo ukryty. Wyniki będą dostępne niebawem.</center>');
		}
	?>
	<table class="results" id="summary" style="width: 90%; margin-right: 5%; margin-top: 4vmax;">
		<tr>
			<td><?php echo($row['submission_time']); ?></td>
			<td><?php echo($row['verification_time']); ?></td>
			<td><i class='fas fa-cloud'></i>&nbsp;Algorytmiczne</td>
			<td style="background-image: <?php echo($gradient); ?>"><?php if(strtotime($row['result_publish_time'])<strtotime("now")) { echo($status); } ?></td>
			<td><?php if(strtotime($row['result_publish_time'])<strtotime("now")) { echo($row['score']); } ?>/<?php echo($row['maxpoints']); ?></td>
			<td style="background-image: <?php echo($gradient); ?>"><?php if(strtotime($row['result_publish_time'])<strtotime("now")) { echo($percentage); } ?></td>
		</tr>
	</table>
	<br style="clear: both;" />
	<br />
	<br />
</div>
<div class="window" id="details">
	<h3 class="window_title">Wyniki szczegółowe</h3>
	<br />
	<table class="results">
		<?php
			foreach($results as $r)
			{
				if($r['anws_correct']==0 and $r['anws_resource']>0)
				{
					$rcolor = "#ffc117";
				} else if($r['anws_wrong']==0 and $r['anws_resource']==0 and $r['anws_correct']>0)
				{
					$rcolor = "#00d10a";
				} else if($r['anws_wrong']>0 and $r['anws_resource']==0 and $r['anws_correct']==0)
				{
					$rcolor = "#ff3d6e";
				} else {
					$rcolor = "#8eed28";
				}
				echo('<tr>
				<td><b>Pakiet '.$r['test_id'].'</b></td>
				<td><b><i class="fas fa-clock"></i>&nbsp;&nbsp;'.(float)$r['time'].'/'.$r['max_time'].'s</b></td>');
				echo('<td style="background-color: '.$rcolor.'; color: #313136;">'.$r['comment'].'</td>
				<td style="background-image: linear-gradient(to left,'.$rcolor.' 0%,transparent 50%);">'.($r['anws_correct']/($r['anws_correct']+$r['anws_wrong']+$r['anws_resource'])*100).'%</td>
				</tr>');
			}
		?>
	</table>
	<br />
	<br />
</div>
<div class="window">
	<h3 class="window_title">Twój kod</h3>
	<br />
	<div style="user-select: text; margin-left: 5%; width: 88%; padding: 1% 1%; background-color: #dae2e6; color: #2a2c2e;">
		<code>
			<pre style="overflow: auto;"><?php
				$codefile = fopen(__DIR__."/../../worker/solutions/".$row['SUBMISSION_ID']."/code/".$row['SUBMISSION_ID'].".".$row['submission_lang'],'r');
				while(!feof($codefile))
				{
					echo(htmlentities(fgets($codefile)));
				}
				fclose($codefile);
			?></pre>
		</code>
	</div>
	<br />
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