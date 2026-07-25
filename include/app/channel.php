<?php
	$db_query = $pdo->prepare('SELECT * FROM CHANNELS INNER JOIN USERS ON CHANNELS.author_id=USERS.USER_ID WHERE CHANNEL_ID=:setid');
	$db_query->execute(['setid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);
	$isfound = 0;

	while($row = $db_query->fetch())
	{
		$isfound++;
		$chtitle = $row['title'];
		$chimgpath = $row['img_path'];
		$chdescription = $row['description'];
		$chauthor = $row['username'];
		$chisarchived = $row['isarchived'];
		$chlayout = json_decode($row['layout']);
	}

	if($isfound!=1) 
	{ 
		kick();
	}
?>
<style>
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
		background-color: var(--container-hover-bg); 
		font-family: inherit;
		cursor: pointer;
		transition: 0.4s;
		text-decoration: none;
	}
	.window .forminput:hover {
		background-color: var(--container-hover-bg-textbox);
	}

	#set_header_img {
		width: calc(100% - 18vw);
		height: 10vmax;
		filter: brightness(20%);
		position: absolute;
		object-fit: cover;
		z-index: -1;
		-webkit-mask-image: linear-gradient(to bottom, black 70%, transparent 100%);
		mask-image: linear-gradient(to bottom, black 70%, transparent 100%);
	}

	.forminput_2 {
		background: transparent;
		outline: none;
		border: none;
		border-bottom: 0.3vmin solid rgb(204, 204, 204);
		font: inherit;
		width: 98%;
		margin-left: auto;
		margin-right: auto;
		margin-top: 2vmin;
		padding: 1% 1%;
	}

	.button {
		padding: 1vw 1vw;
		float: right;

		background-color: #00b3ff;
		color: white;
		border-radius: 5px;
		cursor: pointer;
		transition: 0.2s;
		text-decoration: none;
	}
	.button:hover {
		background-color: #6ed4ff;
	}

	#channel_content {
		position: relative;
		padding-left: 2vmax;
		margin-top: 5vmax;
	}
	#channel_content::before {
		content: '';
		position: absolute;
		top: 0;
		bottom: 0;
		left: 2.2vmax;
		width: 0;
		border-left: 0.25vmax dotted rgba(255, 255, 255, 0.4); 
		z-index: 1;
	}
	.channel_content_chapter {
		width: calc(90% - 1.4vmax);
		margin-left: 5%;
		margin-bottom: 0.3vmax;
		padding: 0.7vmax;
		display: flex;
		flex-direction: column;
		gap: 0.2vmax;
		background-color: var(--container-hover-bg);
		border-radius: 0.6vmax;
	}
	.channel_content_chapter_pinned {
		text-align: center;
	}
	.channel_content_chapter::after {
		content: '●';
		margin-left: -3.3vmax;
		background-color: black;
		padding: 0.4vmax;
		padding-top: 0.2vmax;
		padding-bottom: 0.6vmax;
		border-radius: 1.2vmax;
		border: 0.1vmax solid var(--text);
		width: 1vmax;
		height: 1vmax;
		display: flex;
		align-content: center;
		justify-content: center;
		z-index: 2;
	}
	.channel_content_block_final {
		width: calc(90%);
		margin-left: 5%;
		padding: 0.2vmax;
		margin-bottom: 0.3vmax;
		display: flex;
		gap: 0.5vmax;
	}
	.channel_content_block_final * {
		transition: 0.5s;
	}
	.channel_content_block_final:hover .channel_content_block_title {
		box-shadow: inset 0 0 1vmax rgba(0, 0, 0, 0.4);
		cursor: pointer;
	}
	.channel_content_block_final:hover .channel_content_block_icon,
	.channel_content_block_final:hover .channel_content_block_available_attempts {
		box-shadow: inset 0 0 1vmax rgba(0, 0, 0, 0.4);
		background-color: var(--text) !important;
		color: var(--container-hover-bg-textbox);
		cursor: pointer;
	}
	.channel_content_block_final:hover .channel_content_block_progress_bar {
		background-color: transparent !important;
		width: 70%;
	}
	.channel_content_block_final:hover .channel_content_block_progress {
		width: 3.5vmax;
	}
	.channel_content_chapter .channel_content_block_final {
		width: calc(100% - 0.2vmax);
		margin-left: 0;
	}
	.channel_content_block_icon {
		padding: 1vmax;
		border-radius: 0.4vmax;
		display: flex;
		align-items: center;
	}
	.channel_content_block_title {
		padding: 1vmax;
		border-radius: 0.4vmax;
		background-color: var(--bg);
		margin-right: 0.3vmax;
		display: flex;
		flex-grow: 5;
		align-items: center;
		min-width: 0;
	}
	.channel_content_block_title_text {
		flex-grow: 5;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		min-width: 0;
	}
	.channel_content_block_element_details {
		display: flex;
		flex-direction: row;
		gap: 1vmax;
	}
	.channel_content_block_available_attempts {
		width: auto;
		margin-left: 1vmax;
		justify-content: center;
		text-align: center;
		padding: 0.5vmax;
		border-radius: 0.5vmax;
	}
	.channel_content_block_progress {
		background-color: var(--container-hover-bg);
		width: 5vmax;
		border-radius: 0.5vmax;
		overflow: hidden;
		display: flex;
		align-items: center;
	}
	.channel_content_block_progress_bar {
		padding-top: 0.5vmax;
		padding-bottom: 0.5vmax;
		width: calc(5vmax * 0.2);
		height: 100%;
		display: flex;
		align-items: center;
	}
	.channel_content_block_progress_bar span {
		margin-left: 0.5vmax;
	}
	
	
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.7/Sortable.js" integrity="sha512-aUIczPo0e7N7BM7pIVe8I0XbFrT5jy9IjFSHuxhyb64yfHZ4JL10tKpTK/y8tWVB/hcO2YYfvQwbv3o+srga+g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<img id="set_header_img" src="<?php echo htmlspecialchars($chimgpath, ENT_QUOTES, 'UTF-8'); ?>" />
<br />
<br />
<center>
	<h1><?php echo(htmlentities($chtitle)); ?></h1>
</center>
<br />
<br />
<?php
	if(has_a_priority(3))
	{
		echo('
<div id="new_block_dialog" style="display: none; justify-content: center; align-items: center; margin: 0; min-width: 100vw; min-height: 100vh; background-color: rgba(0,0,0,0.6); position: fixed; top: 0; left: 0; z-index: 999">
	<span onClick="document.getElementById(\'new_block_dialog\').style.display = \'none\';" style="font-size: 4.5vmax; float: right; margin-right: 2vw; cursor: pointer; position: fixed; top: 0; right: 0;">×</span>
	<div style="background-color: #dae2e6; color: black; width: 30vmax; max-height: 80vh; padding: 1vmax 1vmax; border-radius: 0.2vmax;">
		<h2 style="text-align: center;">Dodaj blok (zbiór zadań)</h2>
		<br />
		<form method="POST" action="process.php?r=create_problemset&cid='.filter_var($_GET['id'], FILTER_VALIDATE_INT).'" id="new_block_form">
			<input name="new_block_form_name" id="new_block_form_name" class="forminput_2" type="text" placeholder="Nazwa bloku" required/>
			<br />
			<br />
			<br />
			Dostępność zależy od ukończenia:
			<select class="forminput_2" name="new_block_form_condition" id="new_block_form_condition">
				<option value="none"><i>Niczego</i></option>');

		$db_query = $pdo->prepare('SELECT * FROM PROBLEMSETS WHERE channel_id=:cid');
		$db_query->execute(['cid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);

		while($row = $db_query->fetch()) {
			echo('<option value="S'.$row['SET_ID'].'">'.$row['title'].'</option>');
		}

		echo('</select>
			<br />
			<br />
			<br />
			Czas publikacji:
			<input name="new_block_form_publish_time" id="new_block_form_publish_time" class="forminput_2" type="datetime-local" required/>
			<br />
		</form>
		<br />
		<a class="button" id="button_1" onClick="document.getElementById(\'new_block_form\').submit();" style="margin-right: 1%; margin-bottom: 1%;"><i class="fa fa-plus"></i>&nbsp;Dodaj blok</a>
		<br style="clear: both;"/>
	</div>
</div>');
	}
?>
<div style="display: flex; gap: 1vmax; align-items: flex-start;">
	<div class="window" style="margin-right: 0; min-width: 50%;">
		<h2 class="window_title" style="float: left;"><i class='fas fa-eye'></i>&nbsp;&nbsp;Dostępne dla Ciebie</h2>
		<?php if(has_a_priority(3)) echo('<a onClick="document.getElementById(\'new_block_dialog\').style.display = \'flex\';" class="forminput" style="float: right; margin-right: 5%; margin-top: 2.5%;">Dodaj blok</a>'); ?>
		<div id="channel_content">
			<?php
				function generate_content($data, $pdo): void
				{
					foreach ($data->content as $object) {
						$object = json_decode(json_encode($object), true);
						if($object['type']=="problemset")
						{
							$db_query = $pdo->prepare('SELECT * FROM PROBLEMSETS WHERE SET_ID=:sid');
							$db_query->execute(['sid' => $object['id']]);
							$set = $db_query->fetch();
							$availability = check_problemset_availability($object['id'], $pdo);
							if(isset($set))
							{
								echo('
								<div id="update_block_dialog_'.$set['SET_ID'].'" style="display: none; justify-content: center; align-items: center; margin: 0; min-width: 100vw; min-height: 100vh; background-color: rgba(0,0,0,0.6); position: fixed; top: 0; left: 0; z-index: 999">
									<span onClick="document.getElementById(\'update_block_dialog_'.$set['SET_ID'].'\').style.display = \'none\';" style="font-size: 4.5vmax; float: right; margin-right: 2vw; cursor: pointer; position: fixed; top: 0; right: 0;">×</span>
									<div style="background-color: #dae2e6; color: black; width: 30vmax; max-height: 80vh; padding: 1vmax 1vmax; border-radius: 0.2vmax;">
										<h2 style="text-align: center;">'.htmlentities($set['title']).'</h2>
										<br />
										<form method="POST" action="process.php?r=modify_problemset&sid='.$set['SET_ID'].'" id="update_block_form_'.$set['SET_ID'].'">
											<input name="update_block_form_name" id="update_block_form_name" class="forminput_2" type="text" placeholder="Nazwa bloku" value="'.htmlentities($set['title']).'" required/>
											<br />
											<br />
											<br />
											Dostępność zależy od ukończenia:
											<select class="forminput_2" name="update_block_form_condition" id="update_block_form_condition">
												<option value="none"><i>Niczego</i></option>');

										$db_query = $pdo->prepare('SELECT * FROM PROBLEMSETS WHERE channel_id=:cid');
										$db_query->execute(['cid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);

										while($row = $db_query->fetch()) {
											if($row['SET_ID']==$availability['condition_id'])
											{
												echo('<option value="S'.$row['SET_ID'].'" selected>'.$row['title'].'</option>');
											} else {
												echo('<option value="S'.$row['SET_ID'].'">'.$row['title'].'</option>');
											}
										}

										echo('</select>
											<br />
											<br />
											<br />
											Czas publikacji:
											<input name="update_block_form_publish_time" id="update_block_form_publish_time" class="forminput_2" type="datetime-local" value="'.htmlentities($set['publish_time']).'" required/>
											<br />
										</form>
										<br />
										<a class="button" id="button_1" onClick="document.getElementById(\'update_block_form_'.$set['SET_ID'].'\').submit();" style="margin-right: 1%; margin-bottom: 1%;"><i class="fa fa-plus"></i>&nbsp;Zapisz ustawienia</a>
										<br style="clear: both;"/>
									</div>
								</div>');
								echo('<div class="channel_content_block channel_content_chapter">');
								$db_query = $pdo->prepare('SELECT * FROM PROBLEMS WHERE problemset=:pid');
								$db_query->execute(['pid' => $object['id']]);
								$set_results[$set['SET_ID']] = [];
								if($availability['is_available'] or has_a_priority(3))
								{
									while($row = $db_query->fetch())
									{
										switch($row['type']){
											case 1:
												$contenttype = problem_type_identification('alg');
												break;
											case 2:
												$contenttype = problem_type_identification('ctf');
												break;
											case 3:
												$contenttype = problem_type_identification('och');
												break;
											case 4:
												$contenttype = problem_type_identification('mch');
												break;
											case 5:
												$contenttype = problem_type_identification('opn');
												break;
										}

										$cdb_query = $pdo->prepare('SELECT COUNT(*) AS attempts, MAX(SCORE) AS maxscore FROM SUBMISSIONS WHERE problem_id=:pid AND user_id=:uid');
										$cdb_query->execute(['pid' => $row['PROBLEM_ID'], 'uid' => $_SESSION['AUTH_ID']]);
										$ctx = $cdb_query->fetch();

										if((int)$ctx['maxscore']!=-1)
										{
											$maxscore = $ctx['maxscore'];
										} else {
											$maxscore = 0;
										}
										echo('<a class="channel_content_block_final" href="index.php?p=problem&id='.$row['PROBLEM_ID'].'" style="color: inherit;">
											<div class="channel_content_block_icon" style="background-color: '.$contenttype['color'].';"><i class="'.$contenttype['icon'].'"></i></div>
											<div class="channel_content_block_title">
												<b class="channel_content_block_title_text">#'.$row['PROBLEM_ID'].'&nbsp;&nbsp;'.$row['title'].'</b>
												<div class="channel_content_block_element_details">
													<div class="channel_content_block_available_attempts" style="background-color: '.$contenttype['color'].'">
														'.($row['maxattempts']-$ctx['attempts']).' prób
													</div>
													<div class="channel_content_block_progress">
														<div class="channel_content_block_progress_bar" style="width: calc(5vmax * '.floatval($maxscore/$row['maxpoints']).'); background-color: '.$contenttype['color'].';"><span>'.round($maxscore/$row['maxpoints']*100, 0).'%</span></div>
													</div></div>
											</div>
										</a>');
									}
								} else {
									echo('<h1 style="text-align: center; margin-bottom: -1vmax;"><i class=\'fas fa-lock\'></i></h1>');
								}
								echo('<div class="channel_content_chapter_pinned" style="order: -9999;">'.$set['title']);
								if (!$availability['is_available']) {
									echo('&emsp;<small style="color: rgba(128, 128, 128, 1);"><i class=\'fas fa-lock\'></i> Wymaga ukończenia: '.htmlentities($availability['condition_title']).'</small>');
								} else if(in_array(0, $set_results[$set['SET_ID']]) || count($set_results[$set['SET_ID']])==0)
								{
									echo('&emsp;<small style="color: rgba(218, 130, 6, 1);"><i class=\'fas fa-rocket\'></i> W trakcie</small>');
								} else {
									echo('&emsp;<small style="color: rgba(14, 149, 109, 1);"><i class=\'fas fa-award\'></i> Ukończono</small>');
								}
								echo('</div>');
								if(has_a_priority(3))
								{
									echo('<div class="channel_content_chapter_pinned" style="order: 9999; margin-top: 0;">
										<a href="?p=addproblem&sid='.$object['id'].'" class="forminput"><i class=\'fas fa-plus\'></i>&nbsp;&nbsp;Dodaj zadanie</a>
										<a class="forminput" onClick="document.getElementById(\'update_block_dialog_'.$object['id'].'\').style.display = \'flex\';"><i class=\'fas fa-wrench\'></i>&nbsp;&nbsp;Ustawienia</a>');
								}
								echo('
									<br />
									<br />
								</div></div>');
							}
						}
					}
				}
				generate_content($chlayout, $pdo);
				
			?>
		</div>
		<br />
		<br />
	</div>
	<script type="text/javascript">
		var elements = document.getElementsByClassName('channel_content_block');

		new Sortable(document.getElementById('channel_content'), {
			group: 'shared',
			animation: 150,
			fallbackOnBody: true,
			swapThreshold: 0.65
		});
	</script>
	<div class="window" style="margin-left: 0; align-self: stretch;">
		<h2 class="window_title"><i class='fas fa-medal'></i>&nbsp;&nbsp;Wyniki</h2>
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

						foreach(array_keys($scores) as $u)
						{
							array_push($temp_table_total_scores, $scores[$u]['user_data']['total_score']);
							array_push($temp_table_user_ids, $scores[$u]['user_data']['user_id']);
						}


						array_multisort($temp_table_total_scores, SORT_DESC, $temp_table_user_ids);

						foreach($temp_table_user_ids as $u)
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
</div>
<div class="window" style="display: flex; gap: 2%;">
	<div style="width: 68%;">
		<h3 class="window_title" style="margin-left: 2.5%;">Informacje</h3>
		<p style="margin-left: 2.5%;"><?php echo(htmlentities($chdescription)); ?></p>
		<br />
		<?php
			if(has_a_priority(3))
			{
				echo('
				<a class="button" href="process.php?r=archive_channel&cid='.filter_var($_GET['id'], FILTER_VALIDATE_INT).'" style="float: left; margin-left: 2.5%;"><i class=\'fas fa-archive\'></i>&nbsp;&nbsp;'.($chisarchived==1 ? 'Odarchiwizuj' : 'Archiwizuj').'</a>
				<br style="clear: both;"/>');
			}
		?>
		<br />
	</div>
	<div style="width: 30%;">
		<h3 class="window_title">Autor</h3>
		<div style="font-size: 1vmax; margin-left: 5%; user-select: none;">
			<p><img src="https://api.dicebear.com/10.x/identicon/svg?seed=<?php echo($chauthor); ?>" style="height: 1vmax; border-radius: 0.5vmax; background-color: var(--text); margin-bottom: -0.2vmax;" />
			&nbsp;<?php echo($chauthor); ?></p>
		</div>
		<br />
	</div>
</div>
<br style="clear: both;" />
<br />
<br />