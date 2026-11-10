<style>
	.window a {
		color: var(--highlight-color);
		font-weight: bold;
		text-decoration: none;
	}

	.window .forminput {
		border: 0; 
		padding: 1vw 1.5vw; 
		color: var(--text); 
		background-color: var(--container-hover-bg); 
		font-family: inherit;
		cursor: pointer;
		transition: 0.4s;
	}
	.window .forminput:hover {
		background-color: var(--bg);
	}

	.window .choice_option {
		appearance: none;
		background-color: var(--bg);
		padding: 0.40vw 0.40vw;
		border: 0.25vw solid var(--bg);
		transform: translateY(0.4vw);
		border-radius: 0.2vw;
		transition: 0.3s;
		cursor: pointer;
	}
	.window .choice_option:checked {
		background-color: var(--highlight-color);
		padding: 0.40vw 0.40vw;
	}
	.window label {
		cursor: pointer;
	}


</style>
<form method="POST" id="send_answers" action="process.php?r=verify_test&id=<?php echo($_GET['id']); ?>">
	<?php
		$db_query = $pdo->prepare('SELECT * FROM TEST_QUESTIONS WHERE problem_id=:pid');
		$db_query->execute(['pid' => $_GET['id']]);
		$count = 0;

		while($row = $db_query->fetch())
		{
			$answers = json_decode($row['answers']);
			$count++;
			echo('<div class="window">
				<p style="margin-left: 5%;">
					<b>Pytanie '.$count.': '.htmlentities($row['question']).'</b>&emsp; 1 pkt<br /><br />
					<input type="radio" class="choice_option" name="'.$count.'" id="'.$count.'_a" value="a" />
					<label for="'.$count.'_a">'.htmlentities($answers->{'a'}).'</label><br />
					<input type="radio" class="choice_option" name="'.$count.'" id="'.$count.'_b" value="b" />
					<label for="'.$count.'_b">'.htmlentities($answers->{'b'}).'</label><br />
					<input type="radio" class="choice_option" name="'.$count.'" id="'.$count.'_c" value="c" />
					<label for="'.$count.'_c">'.htmlentities($answers->{'c'}).'</label><br />
					<input type="radio" class="choice_option" name="'.$count.'" id="'.$count.'_d" value="d" />
					<label for="'.$count.'_d">'.htmlentities($answers->{'d'}).'</label><br />
				</p>
			</div>');
		}
	?>
	<div class="window">
		<br />
		<center>
			<p><?php echo(__("That's all")); ?>!</p>
			<input name="csrf_token" type="hidden" value="<?php echo($_SESSION['CSRF_TOKEN']); ?>" />
			<input type="submit" value="<?php echo(__("Submit your solution")); ?>" class="forminput"/>
		</center>
		<br />
	</div>
</form>
<br />
<br />