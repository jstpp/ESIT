<?php
	$db_query = $pdo->prepare('SELECT * FROM PROBLEMSETS WHERE SET_ID=:setid');
	$db_query->execute(['setid' => filter_var($_GET['sid'], FILTER_VALIDATE_INT)]);
	$isfound = 0;

	while($row = $db_query->fetch())
	{
		$isfound++;
		$settitle = $row['title'];
		$setid = $row['SET_ID'];
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

		border: 0.2vw solid var(--container-hover-bg);
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
		color: var(--text); 
		background-color: var(--container-hover-bg); 
		font-family: inherit;
		outline: none;
	}
	.window .forminput_a {
		border: 0; 
		padding: 0.75vw 2.5%; 
		color: var(--text); 
		background-color: var(--container-hover-bg); 
		font-family: inherit;
		cursor: pointer;
		transition: 0.4s;
		text-decoration: none;
	}
	.window .forminput_a:hover {
		background-color: var(--bg);
	}

	.window .test_set, .test_set_question, .test_set_question_m {
		margin-left: 2.5%;
		width: 91%;
		background-color: var(--bg);
		color: white;
		padding: 1% 2%;
		margin-top: 0.5vmax;
	}
	.window input, .window select {
		font: inherit;
	}

	.data-invalid {
        color: rgb(184, 1, 0);
        margin-left: 1vmax;
    }

    .data-invalid:before {
        position: relative;
        left: -1vmax;
        content: "✖";
    }

    .data-valid {
        color: rgb(5, 149, 0);
        margin-left: 1vmax;
    }

    .data-valid:before {
        position: relative;
        left: -1vmax;
        content: "✔";
    }

	.problem_type_select_flexbox_option {
		padding: 1vmax;
		flex-grow: 1;
		width: 33%;
		height: 10vmax;
		transition: 0.3s;
		cursor: pointer;
		overflow: hidden;
		border-radius: 0.5vmax;
	}
	.problem_type_select_flexbox_option * {
		user-select: none;
	}
	.problem_type_select_flexbox_option i {
		font-size: 8vmax;
	}
	.problem_type_select_flexbox_option:hover {
		filter: brightness(70%);
	}
</style>
<br />
<br />
<div id="problem_type_select">
	<div class="window" id="problem_type_select_window">
		<h2 style="margin-left: 2.5%;">Wybierz typ zadania</h1>
		<div id="problem_type_select_flexbox" style="margin: 2.5%; display: flex; flex-wrap: wrap; gap: 0.5vmax;">
			<div class="problem_type_select_flexbox_option" onClick="validate_data(); typeSelect(1);" style="background-color: rgba(0, 121, 250, 1);">
				<h3>Algorytmiczne</h3><br />
				<i class="fa-solid fa-code" style="color: rgba(0, 0, 0, 0.2);"></i>
			</div>
			<div class="problem_type_select_flexbox_option" onClick="validate_data(); typeSelect(2);" style="background-color: rgba(208, 72, 72, 1);">
				<h3>Capture The Flag</h3><br />
				<i class="fa-solid fa-flag" style="color: rgba(0, 0, 0, 0.2);"></i>
			</div>
			<div class="problem_type_select_flexbox_option" onClick="validate_data(); typeSelect(3);" style="background-color: rgba(14, 149, 109, 1);">
				<h3>Pytania jednokrotnego wyboru</h3><br />
				<i class="fa fa-check-square-o" style="color: rgba(0, 0, 0, 0.2);"></i>
			</div>
			<div class="problem_type_select_flexbox_option" onClick="validate_data(); typeSelect(4);" style="background-color: rgba(218, 130, 6, 1);">
				<h3>Pytania wielokrotnego wyboru</h3><br />
				<i class="fa fa-check-square" style="color: rgba(0, 0, 0, 0.2);"></i>
			</div>
			<div class="problem_type_select_flexbox_option" onClick="validate_data(); typeSelect(5);" style="background-color: rgba(69, 47, 165, 1);">
				<h3>Zadanie otwarte</h3><br />
				<i class="fa fa-pencil-square-o" style="color: rgba(0, 0, 0, 0.2);"></i>
			</div>
		</div>
	</div>
</div>
<div id="problem_add_window" style="display: none;">
	<center>
		<h1>Nowe zadanie (w: <?php echo(htmlentities($settitle)); ?>)</h1>
	</center>
	<br />
	<br />
	<form method="POST" action="process.php?r=add_problem&sid=<?php echo(htmlentities($setid)); ?>" enctype="multipart/form-data" id="newproblemform">
		<div class="window" id="main_info">
			<h2 class="window_title" style="margin-left: 2.5%;">Podstawowe informacje</h2>
			<input type="text" name="problem_title" onChange="validate_data();" placeholder="Tytuł zadania" class="forminput" style="margin-left: 2.5%; width: 91.75%;" required />
			<br />
			<input type="hidden" id="problem_type" name="problem_type" value="0" />
			<br />
			<input type="number" name="problem_points" onChange="validate_data();" placeholder="Maks. liczba punktów" class="forminput" min="0" max="10000" style="margin-left: 2.5%; width: 15.5%; float: left;" required />
			<input type="number" name="problem_maxattempts" onChange="validate_data();" placeholder="Maks. liczba podejść" class="forminput" min="0" style="margin-left: 0.5%; width: 15.5%; float: left;" required />
			<select class="forminput" name="problem_isarchived" onChange="validate_data();" style="margin-right: 2.5%; width: 55.5%; float: right;" required>
				<option value="0">Publiczne</option>
				<option value="1">Ukryte/zarchiwizowane</option>
			</select>
			<br style="clear: both;"/>
			<br />
			<p style="margin-left: 2.5%; width: 95%;">Moment publikacji zadania:&emsp;<input type="datetime-local" onChange="validate_data();" class="forminput" name="publish_time" /></p>
			<p style="margin-left: 2.5%; width: 95%;">Moment publikacji wyników:&emsp;<input type="datetime-local" onChange="validate_data();" class="forminput" name="result_publish_time" /></p>
			<br />
		</div>
		<div class="window" id="alg_pdf">
			<h2 class="window_title" style="margin-left: 2.5%;">Treść</h2>
			<input type="file" style="margin-left: 2.5%;" name="alg_file" accept=".pdf"/>
			<p style="margin-left: 2.5%;">Plik zawierający treść zadania powinien być w formacie PDF.</p>
			<br />
		</div>
		<div class="window" id="alg_tests">
			<h2 class="window_title" style="margin-left: 2.5%;">Pakiety testów</h2>
			<p style="margin-left: 2.5%;">Dopuszczone formaty wejścia: <code>.in</code>, wyjścia: <code>.out</code>.</p>
			<br />
			<div class="test_set" id="tab_1">
				<span style="font-size: 3vmax; float: right; margin-right: 1%; cursor: pointer;">×</span>
				<p><b>Pakiet 1:</b></p>
				<p>Wejście&emsp;
				<input type="file" name="in_1" id="in_1" accept=".in"/></p>
				<p>Wyjście&emsp;
				<input type="file" name="out_1" id="out_1" accept=".out"/></p>
				<br />
				<input type="number" name="time_1" id="time_1" placeholder="Maksymalny czas [s]" class="forminput" style="width: 30%;" step="0.01" max="600" min="0"/>
				<br />
				<br />
				<input type="number" name="memory_1" id="memory_1" placeholder="Maksymalna pamięć [MiB]" class="forminput" style="width: 30%;" step="16" max="1024" min="16"/>
				<br />
				<br />
			</div>
			<br />
			<a class="forminput_a" id="add_btn" onClick="add_tab();" style="float: right; margin-right: 2.5%;"><i class="fa fa-plus"></i>&nbsp;&nbsp;Dodaj nowy</a>
			<br style="clear: both;"/>
			<br />
			<script>
				function add_tab()
				{
					let sets_count = document.querySelectorAll('.test_set').length;
					new_tab = document.querySelector('.test_set:last-of-type').cloneNode(true)
					new_tab.id = "tab_" + (sets_count+1);
					document.getElementById("tab_" + sets_count).querySelector("span").style.display = 'none';

					const tab_label = new_tab.querySelector("p b");
					tab_label.textContent = "Pakiet " + (sets_count+1) + ":";

					const in_input = new_tab.querySelector("input[name^='in_']");
					const out_input = new_tab.querySelector("input[name^='out_']");
					const time_input = new_tab.querySelector("input[name^='time_']");
					const memory_input = new_tab.querySelector("input[name^='memory_']");

					in_input.name = "in_" + (sets_count+1);
					in_input.id = "in_" + (sets_count+1);
					in_input.value = ""

					out_input.name = "out_" + (sets_count+1);
					out_input.id = "out_" + (sets_count+1);
					out_input.value = "";

					time_input.name = "time_" + (sets_count+1);
					time_input.id = "time_" + (sets_count+1);

					memory_input.name = "memory_" + (sets_count+1);
					memory_input.id = "memory_" + (sets_count+1);

					const closeBtn = new_tab.querySelector("span");
					closeBtn.addEventListener("click", function () {
						document.getElementById("tab_" + (sets_count + 1)).remove();
						document.getElementById("tab_" + sets_count).querySelector("span").style.display = 'block';
					});

					document.querySelector('.test_set:last-of-type').after(new_tab);
				}
			</script>
		</div>
		<div class="window" id="ctf_file">
			<h2 class="window_title" style="margin-left: 2.5%;">Plik (Capture The Flag)</h2>
			<input type="file" style="margin-left: 2.5%;" name="ctf_file"/>
			<br />
			<br />
		</div>
		<div class="window" id="ctf_flag">
			<h2 class="window_title" style="margin-left: 2.5%;">Flaga (Capture The Flag)</h2>
			<input type="text" name="ctf_flag" placeholder="Poprawna flaga" class="forminput" style="margin-left: 2.5%; width: 91.75%;" required />
			<br />
			<br />
		</div>
		<div class="window" id="test_questions_single">
			<h2 class="window_title" style="margin-left: 2.5%;">Pytania</h2>
				<div class="test_set_question" id="q_tab_1">
					<span style="font-size: 3vmax; float: right; margin-right: 1%; cursor: pointer;">×</span>
					<p><b>Pytanie 1:</b></p>
					<input type="text" placeholder="Pytanie" class="forminput" style="width: 96%;" name="s_question_1" id="s_question_1"/>
					<br />
					<hr />
					<input type="text" placeholder="Odpowiedź a" class="forminput" style="width: 21%;" name="q_a_1" id="q_a_1"/>
					<input type="text" placeholder="Odpowiedź b" class="forminput" style="width: 21%;" name="q_b_1" id="q_b_1"/>
					<input type="text" placeholder="Odpowiedź c" class="forminput" style="width: 21%;" name="q_c_1" id="q_c_1"/>
					<input type="text" placeholder="Odpowiedź d" class="forminput" style="width: 21%;" name="q_d_1" id="q_d_1"/>
					<br />
					<hr />
					<p>Poprawna odpowiedź:&emsp;
					<input type="radio" value="a" name="correct_1" id="correct_1"/>a
					<input type="radio" value="b" name="correct_1" id="correct_1"/>b
					<input type="radio" value="c" name="correct_1" id="correct_1"/>c
					<input type="radio" value="d" name="correct_1" id="correct_1"/>d
					</p>
				</div>
				<br />
			<a class="forminput_a" id="add_btn" onClick="add_q_tab();" style="float: right; margin-right: 2.5%;"><i class="fa fa-plus"></i>&nbsp;&nbsp;Dodaj nowe</a>
			<br style="clear: both;" />
			<br />
			<script>
				function add_q_tab()
				{
					let sets_q_count = document.querySelectorAll('.test_set_question').length;
					new_tab = document.querySelector('.test_set_question:last-of-type').cloneNode(true)
					new_tab.id = "q_tab_" + (sets_q_count+1);
					document.getElementById("q_tab_" + sets_q_count).querySelector("span").style.display = 'none';

					const tab_label = new_tab.querySelector("p b");
					tab_label.textContent = "Pakiet " + (sets_q_count+1) + ":";

					const question = new_tab.querySelector("input[name^='s_question_']");
					const correct_input_a = new_tab.querySelector("input[name^='correct_'][value='a']");
					const correct_input_b = new_tab.querySelector("input[name^='correct_'][value='b']");
					const correct_input_c = new_tab.querySelector("input[name^='correct_'][value='c']");
					const correct_input_d = new_tab.querySelector("input[name^='correct_'][value='d']");

					const anwser_a_input = new_tab.querySelector("input[name^='q_a_']");
					const anwser_b_input = new_tab.querySelector("input[name^='q_b_']");
					const anwser_c_input = new_tab.querySelector("input[name^='q_c_']");
					const anwser_d_input = new_tab.querySelector("input[name^='q_d_']");

					question.name = "s_question_" + (sets_q_count + 1);
					question.id = "s_question_" + (sets_q_count + 1);

					anwser_a_input.name = "q_a_" + (sets_q_count + 1);
					anwser_a_input.id = "q_a_" + (sets_q_count + 1);

					anwser_b_input.name = "q_b_" + (sets_q_count + 1);
					anwser_b_input.id = "q_b_" + (sets_q_count + 1);
					
					anwser_c_input.name = "q_c_" + (sets_q_count + 1);
					anwser_c_input.id = "q_c_" + (sets_q_count + 1);

					anwser_d_input.name = "q_d_" + (sets_q_count + 1);
					anwser_d_input.id = "q_d_" + (sets_q_count + 1);

					correct_input_a.name = "correct_" + (sets_q_count + 1);
					correct_input_a.id = "correct_" + (sets_q_count + 1);
					correct_input_b.name = "correct_" + (sets_q_count + 1);
					correct_input_b.id = "correct_" + (sets_q_count + 1);
					correct_input_c.name = "correct_" + (sets_q_count + 1);
					correct_input_c.id = "correct_" + (sets_q_count + 1);
					correct_input_d.name = "correct_" + (sets_q_count + 1);
					correct_input_d.id = "correct_" + (sets_q_count + 1);


					const closeBtn = new_tab.querySelector("span");
					closeBtn.addEventListener("click", function () {
						document.getElementById("q_tab_" + (sets_q_count + 1)).remove();
						document.getElementById("q_tab_" + sets_q_count).querySelector("span").style.display = 'block';
					});

					document.querySelector('.test_set_question:last-of-type').after(new_tab);
				}
			</script>
		</div>
		<div class="window" id="test_questions_multiple">
			<h2 class="window_title" style="margin-left: 2.5%;">Pytania</h2>
				<div class="test_set_question_m" id="qm_tab_1">
					<span style="font-size: 3vmax; float: right; margin-right: 1%; cursor: pointer;">×</span>
					<p><b>Pytanie 1:</b></p>
					<input type="text" placeholder="Pytanie" class="forminput" style="width: 96%;" name="m_question_1" id="m_question_1"/>
					<br />
					<hr />
					<input type="text" placeholder="Odpowiedź a" class="forminput" style="width: 21%;" name="qm_a_1" id="qm_a_1"/>
					<input type="text" placeholder="Odpowiedź b" class="forminput" style="width: 21%;" name="qm_b_1" id="qm_b_1"/>
					<input type="text" placeholder="Odpowiedź c" class="forminput" style="width: 21%;" name="qm_c_1" id="qm_c_1"/>
					<input type="text" placeholder="Odpowiedź d" class="forminput" style="width: 21%;" name="qm_d_1" id="qm_d_1"/>
					<br />
					<hr />
					<p>Poprawna odpowiedź:&emsp;
					<input type="checkbox" value="a" name="correct_a_1" id="correct_a_1"/>a
					<input type="checkbox" value="b" name="correct_b_1" id="correct_b_1"/>b
					<input type="checkbox" value="c" name="correct_c_1" id="correct_c_1"/>c
					<input type="checkbox" value="d" name="correct_d_1" id="correct_d_1"/>d
					</p>
				</div>
				<br />
			<a class="forminput_a" id="add_btn" onClick="add_qm_tab();" style="float: right; margin-right: 2.5%;"><i class="fa fa-plus"></i>&nbsp;&nbsp;Dodaj nowe</a>
			<br style="clear: both;" />
			<br />
			<script>
				function add_qm_tab()
				{
					let sets_qm_count = document.querySelectorAll('.test_set_question_m').length;
					new_tab = document.querySelector('.test_set_question_m:last-of-type').cloneNode(true)
					new_tab.id = "qm_tab_" + (sets_qm_count+1);
					document.getElementById("qm_tab_" + sets_qm_count).querySelector("span").style.display = 'none';

					const tab_label = new_tab.querySelector("p b");
					tab_label.textContent = "Pakiet " + (sets_qm_count+1) + ":";

					const question = new_tab.querySelector("input[name^='m_question_']");
					const correct_input_a = new_tab.querySelector("input[name^='correct_a_']");
					const correct_input_b = new_tab.querySelector("input[name^='correct_b_']");
					const correct_input_c = new_tab.querySelector("input[name^='correct_c_']");
					const correct_input_d = new_tab.querySelector("input[name^='correct_d_']");

					const anwser_a_input = new_tab.querySelector("input[name^='qm_a_']");
					const anwser_b_input = new_tab.querySelector("input[name^='qm_b_']");
					const anwser_c_input = new_tab.querySelector("input[name^='qm_c_']");
					const anwser_d_input = new_tab.querySelector("input[name^='qm_d_']");

					question.name = "m_question_" + (sets_qm_count + 1);
					question.id = "m_question_" + (sets_qm_count + 1);

					anwser_a_input.name = "qm_a_" + (sets_qm_count + 1);
					anwser_a_input.id = "qm_a_" + (sets_qm_count + 1);

					anwser_b_input.name = "qm_b_" + (sets_qm_count + 1);
					anwser_b_input.id = "qm_b_" + (sets_qm_count + 1);
					
					anwser_c_input.name = "qm_c_" + (sets_qm_count + 1);
					anwser_c_input.id = "qm_c_" + (sets_qm_count + 1);

					anwser_d_input.name = "qm_d_" + (sets_qm_count + 1);
					anwser_d_input.id = "qm_d_" + (sets_qm_count + 1);

					correct_input_a.name = "correct_a_" + (sets_qm_count + 1);
					correct_input_a.id = "correct_a_" + (sets_qm_count + 1);
					correct_input_b.name = "correct_b_" + (sets_qm_count + 1);
					correct_input_b.id = "correct_b_" + (sets_qm_count + 1);
					correct_input_c.name = "correct_c_" + (sets_qm_count + 1);
					correct_input_c.id = "correct_c_" + (sets_qm_count + 1);
					correct_input_d.name = "correct_d_" + (sets_qm_count + 1);
					correct_input_d.id = "correct_d_" + (sets_qm_count + 1);


					const closeBtn = new_tab.querySelector("span");
					closeBtn.addEventListener("click", function () {
						document.getElementById("qm_tab_" + (sets_qm_count + 1)).remove();
						document.getElementById("qm_tab_" + sets_qm_count).querySelector("span").style.display = 'block';
					});

					document.querySelector('.test_set_question_m:last-of-type').after(new_tab);
				}
			</script>
		</div>
	</form>
	<br style="clear: both;" />
	<script>
		function typeSelect(x)
		{
			if(parseInt(x)===1)
			{
				document.getElementById('problem_type').value = 1;
				document.getElementById('alg_pdf').style.display = 'block';
				document.getElementById('alg_tests').style.display = 'block';
				document.getElementById('ctf_file').style.display = 'none';
				document.getElementById('ctf_flag').style.display = 'none';
				document.getElementById('test_questions_single').style.display = 'none';
				document.getElementById('test_questions_multiple').style.display = 'none';
			} else if(parseInt(x)===2) {
				document.getElementById('problem_type').value = 2;
				document.getElementById('alg_pdf').style.display = 'none';
				document.getElementById('alg_tests').style.display = 'none';
				document.getElementById('ctf_file').style.display = 'block';
				document.getElementById('ctf_flag').style.display = 'block';
				document.getElementById('test_questions_single').style.display = 'none';
				document.getElementById('test_questions_multiple').style.display = 'none';
			}  else if(parseInt(x)===3) {
				document.getElementById('problem_type').value = 3;
				document.getElementById('alg_pdf').style.display = 'none';
				document.getElementById('alg_tests').style.display = 'none';
				document.getElementById('ctf_file').style.display = 'none';
				document.getElementById('ctf_flag').style.display = 'none';
				document.getElementById('test_questions_single').style.display = 'block';
				document.getElementById('test_questions_multiple').style.display = 'none';
			} else if(parseInt(x)===4) {
				document.getElementById('problem_type').value = 4;
				document.getElementById('alg_pdf').style.display = 'none';
				document.getElementById('alg_tests').style.display = 'none';
				document.getElementById('ctf_file').style.display = 'none';
				document.getElementById('ctf_flag').style.display = 'none';
				document.getElementById('test_questions_single').style.display = 'none';
				document.getElementById('test_questions_multiple').style.display = 'block';
			} else if(parseInt(x)===5) {
				document.getElementById('problem_type').value = 5;
				document.getElementById('alg_pdf').style.display = 'block';
				document.getElementById('alg_tests').style.display = 'none';
				document.getElementById('ctf_file').style.display = 'none';
				document.getElementById('ctf_flag').style.display = 'none';
				document.getElementById('test_questions_single').style.display = 'none';
				document.getElementById('test_questions_multiple').style.display = 'none';
			} else {
				return -1;
			}

			document.getElementById('problem_add_window').style.display = 'block';
			document.getElementById('problem_type_select').style.display = 'none';
		}
		typeSelect(document.getElementById('type_select'));
	</script>
	<div class="window">
		<br />
		<a class="forminput_a" id="button_1" onClick="document.getElementById('newproblemform').submit();" style="display: none; float: right; margin-right: 2.5%;"><i class="fa fa-plus"></i>&nbsp;&nbsp;Dodaj nowe zadanie</a>
		<a class="forminput_a" href="index.php?p=set&id=<?php echo($setid); ?>" style="float: right; margin-right: 2.5%;"><i class="fa fa-close"></i>&nbsp;&nbsp;Anuluj</a>
		<br style="clear: both;" />
		<br />
		<fieldset id="r_i_validation" style="margin-top: 2vmax; width: 92.5%; margin: auto;">
			<legend>Walidacja danych</legend>
			<p class="data-invalid" id="c_1">Wypełnij wszystkie podstawowe pola</p>
			<script>
				let e = 0;
				function check_data()
				{
					if(e>0)
					{
						document.getElementById('button_1').style.display = 'block';
					} else {
						document.getElementById('button_1').style.display = 'none';
					}
				}

				function validate_data()
				{
					e = 0;
					if(document.querySelector('input[name="problem_type"]').value>0 
					&& document.querySelector('input[name="problem_points"]').value.length>0 
					&& document.querySelector('input[name="publish_time"]').value.length>0
					&& document.querySelector('input[name="result_publish_time"]').value.length>0
					&& document.querySelector('input[name="problem_title"]').value.length>0 
					&& document.querySelector('input[name="problem_maxattempts"]').value.length>0
					&& document.querySelector('select[name="problem_isarchived"]').value.length>0) 
					{
						e++;
						document.getElementById('c_1').classList.add("data-valid");
						document.getElementById('c_1').classList.remove("data-invalid");
					} else {
						document.getElementById('c_1').classList.add("data-invalid");
						document.getElementById('c_1').classList.remove("data-valid");
					}
					check_data();
				}
			</script>
		</fieldset>
		<br style="clear: both;" />
		<br />
	</div>
</div>
<br style="clear: both;" />
<br />