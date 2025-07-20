<style>
	.window {
		width: 95%;
		margin-left: 2.5%;
		margin-top: 1vw;

		background-color: var(--container-bg);

		border: 0.2vw solid #2a2c2e;
		border-radius: 1vw;
		user-select: none;
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

	.window .multiple_choice_option {
		appearance: none;
		background-color: #3e4145;
		padding: 0.40vw 0.40vw;
		border: 0.25vw solid #3e4145;
		transform: translateY(0.4vw);
		border-radius: 0.2vw;
		transition: 0.3s;
		cursor: pointer;
	}
	.window .multiple_choice_option:checked {
		background-color: rgb(0, 179, 255);
		padding: 0.40vw 0.40vw;
	}
	.window label {
		cursor: pointer;
	}


</style>
<form method="POST" id="send_anwsers" action="process.php?r=verify_test&id=<?php echo($_GET['id']); ?>">
	<?php
		$db_query = $pdo->prepare('SELECT * FROM TEST_QUESTIONS WHERE problem_id=:pid');
		$db_query->execute(['pid' => $_GET['id']]);
		$count = 0;

		while($row = $db_query->fetch())
		{
			$anwsers = json_decode($row['anwsers']);
			$count++;
			echo('<div class="window">
				<p style="margin-left: 5%;">
					<b>Pytanie '.$count.': '.$row['question'].'</b>&emsp; 1 pkt<br /><br />
					<input type="checkbox" class="multiple_choice_option" name="'.$count.'_a" id="'.$count.'_a" value="a" />
					<label for="'.$count.'_a">'.$anwsers->{'a'}.'</label><br />
					<input type="checkbox" class="multiple_choice_option" name="'.$count.'_b" id="'.$count.'_b" value="b" />
					<label for="'.$count.'_b">'.$anwsers->{'b'}.'</label><br />
					<input type="checkbox" class="multiple_choice_option" name="'.$count.'_c" id="'.$count.'_c" value="c" />
					<label for="'.$count.'_c">'.$anwsers->{'c'}.'</label><br />
					<input type="checkbox" class="multiple_choice_option" name="'.$count.'_d" id="'.$count.'_d" value="d" />
					<label for="'.$count.'_d">'.$anwsers->{'d'}.'</label><br />
				</p>
			</div>');
		}
	?>
	<div class="window">
		<br />
		<center>
			<p>To wszystko!</p>
			<input type="submit" value="Prześlij odpowiedź" class="forminput"/>
		</center>
		<br />
	</div>
</form>
<br />
<br />