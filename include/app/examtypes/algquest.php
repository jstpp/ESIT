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

	.window a {
		color: rgb(0, 179, 255);
		font-weight: bold;
		text-decoration: none;
	}

	.window .forminput {
		border: 0; 
		padding: 1vw 1.5vw; 
		color: #dae2e6; 
		background-color: #2a2c2e; 
		font-family: inherit;
		cursor: pointer;
		transition: 0.4s;
	}
	.window .forminput:hover {
		background-color: #3e4145;
	}

	#tests td {
		border: 1px solid white;
		padding: 1vmin 3vmin;
	}
</style>

<div class="window">
	<h2 class="window_title">Treść zadania</h2>
	<iframe src="content/quests/<?php echo($problemid); ?>/pdf/<?php echo($problemid); ?>.pdf" style="width: 90%; margin-left: 5%; height: 85vh; border: 0;"></iframe>
	<br />
	<p style="color: white; text-align: center; width: 100%;">To zadanie możesz też otworzyć <a href="content/quests/<?php echo($problemid); ?>/pdf/<?php echo($problemid); ?>.pdf" target="_blank">&nbsp;<i class='fas fa-folder-open'></i>&nbsp;tutaj</a>&nbsp;</p>
	<br />
	<br />
</div>
<?php
	$db_query = $pdo->prepare('SELECT COUNT(*) AS count FROM SUBMISSIONS WHERE problem_id=:setid AND user_id=:uid');
	$db_query->execute(['setid' => filter_var($_GET['id'], FILTER_VALIDATE_INT), 'uid' => $_SESSION['AUTH_ID']]);

	$count = $db_query->fetch()['count'];

	if($maxattempts-$count>1)
	{
		$howmanytimes = "Rozwiązanie do tego zadania możesz wysłać jeszcze <b>".($maxattempts-$count)." razy</b>.";
	} else if ($maxattempts-$count==0)
	{
		$howmanytimes = "Rozwiązanie do tego zadania możesz wysłać jeszcze <b>1 raz</b>.";
	} else {
		$howmanytimes = "Wykorzystałeś_aś już wszystkie próby!";
	}
?>
<div class="window">
	<p style="margin-left: 5%;">
		<i class='fas fa-info-circle'></i>&emsp;<?php echo($howmanytimes); ?><br /><br />
		<b>Limity (wg pakietów testów):</b><br />
		<table style="margin-left: 5%;" id="tests">
			<?php
				$db_query = $pdo->prepare('SELECT * FROM ALG_TEST_LIST WHERE problem_id=:setid');
				$db_query->execute(['setid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);

				while($test = $db_query->fetch())
				{
					echo('<tr>
					<td>Pakiet '.$test['TEST_ID'].'</td>
					<td>'.$test['max_memory'].' MiB</td>
					<td>'.$test['max_time'].' s</td>
				</tr>');
				}
			?>
		</table>
	</p>
</div>
<?php
	if($maxattempts-$count>0)
	{
		echo('
		<div class="window" id="mysolution">
			<form method="POST" action="process.php?r=send_alg_solution&pid='.$problemid.'">
				<h2 class="window_title">Twoje rozwiązanie</h2>
				<br />
				<select class="forminput" name="lang" id="lang" onChange="lchange();" style="float: left; margin-left: 5%;">
					<option value="cpp">C++ (g++ dla C++20)</option>
					<option value="python">Python (Python 3.10)</option>
				</select>
				<input type="submit" value="Prześlij odpowiedź" class="forminput" style="float: right; margin-right: 5%;" onClick="document.getElementById(\'editor_code\').innerHTML = editor.getValue();"/>
				<br style="clear: both;"/>
				<br />
				<div id="sendtext" style="width: 90%; margin-left: 5%;">
					<textarea id="editor_code" name="sendtext" style="display: none;" readonly></textarea>
					<div id="editor" style="position: relative; height: 60vmin; width: 100%; border-radius: 2vmin;"></div>
					<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/ace.js" type="text/javascript" charset="utf-8"></script>
					<script>
						var editor = ace.edit("editor");
						editor.setTheme("ace/theme/'.str_replace('.css','',$settings->{'code_editor_theme'}).'");
						editor.session.setMode("ace/mode/c_cpp");

						function lchange(){
							if(document.getElementById("lang").value === "python")
							{
								editor.session.setMode("ace/mode/python");
							} else if(document.getElementById("lang").value === "cpp")
							{
								editor.session.setMode("ace/mode/c_cpp");
							}
						}
					</script>
				</div>
			</form>
			<br />
		</div>');
	}
?>
<br />
<br />