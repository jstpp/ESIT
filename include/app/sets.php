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
		cursor: pointer;
	}
	.window:hover {
		background-color: var(--container-hover-bg);
	}

	.window .window_title {
		margin-left: 5%;
		margin-top: 1.5vw;
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
</style>

<center>
	<h1>Aktywne zbiory zadań</h1> 
	<?php if(has_a_priority(3)) echo('<a class="button" style="margin-right: 2.5%;"onClick="document.getElementById(\'new_set_dialog\').style.display = \'flex\';">Dodaj nowy</a>'); ?>
	<br style="clear: both;" />
</center>
<?php
	if(has_a_priority(3))
	{
		echo('
<div id="new_set_dialog" style="display: none; justify-content: center; align-items: center; margin: 0; min-width: 100vw; min-height: 100vh; background-color: rgba(0,0,0,0.6); position: fixed; top: 0; left: 0; z-index: 999">
	<span onClick="document.getElementById(\'new_set_dialog\').style.display = \'none\';" style="font-size: 4.5vmax; float: right; margin-right: 2vw; cursor: pointer; position: fixed; top: 0; right: 0;">×</span>
	<div style="background-color: #dae2e6; color: black; width: 30vmax; max-height: 80vh; padding: 1vmax 1vmax; border-radius: 0.2vmax;">
		<h2 style="text-align: center;">Dodaj zbiór zadań</h2>
		<br />
		<form method="POST" id="new_set_form" action="process.php?r=create_set">
			<input name="setname" class="forminput_2" type="text" placeholder="Nazwa zbioru zadań" onChange="validate_data();" required/>
			<br />
			<textarea name="description" class="forminput_2" placeholder="Opis zbioru zadań" onChange="validate_data();" required></textarea>
			<br />
			<input name="publish_time" class="forminput_2" type="datetime-local" onChange="validate_data();" required/>
			<br />
			<br />
			<input name="isactive" id="isactive" type="checkbox" value="1" onChange="validate_data();" checked> 
			<label for="isactive">Czy aktywny?</label>
		</form>
		<br />
		<a class="button" id="button_1" onClick="document.getElementById(\'new_set_form\').submit();" style="display: none; margin-right: 1%; margin-bottom: 1%;"><i class="fa fa-plus"></i>&nbsp;Dodaj zbiór zadań</a>
		<br style="clear: both;"/>
		<fieldset id="r_i_validation" style="margin-top: 2vmax;">
            <legend>Walidacja danych</legend>
            <p class="data-invalid" id="c_1">Wypełnij wszystkie wymagane pola</p>
            <script>
                let e = 0;
                function check_data()
                {
                    if(e>0)
                    {
                        document.getElementById(\'button_1\').style.display = \'block\';
                    } else {
                        document.getElementById(\'button_1\').style.display = \'none\';
                    }
                }

                function validate_data()
                {
                    e = 0;
                    if(document.querySelector(\'input[name="setname"]\').value.length>0 && document.querySelector(\'textarea[name="description"]\').value.length>0 && document.querySelector(\'input[name="publish_time"]\').value.length>0) 
					{
                        e++;
                        document.getElementById(\'c_1\').classList.add("data-valid");
                        document.getElementById(\'c_1\').classList.remove("data-invalid");
                    } else {
                        document.getElementById(\'c_1\').classList.add("data-invalid");
                        document.getElementById(\'c_1\').classList.remove("data-valid");
                    }
                    check_data();
                }
            </script>
        </fieldset>
		<br style="clear: both;"/>
	</div>
</div>');
	}
?>
<?php
	$db_query = $pdo->prepare('SELECT PROBLEMSETS.SET_ID AS sid, PROBLEMSETS.title AS title, USERS.name AS name, USERS.surname AS surname FROM PROBLEMSETS INNER JOIN USERS ON PROBLEMSETS.author_id=USERS.USER_ID WHERE PROBLEMSETS.isarchived=0 AND :current_time>=PROBLEMSETS.publish_time ORDER BY USER_ID DESC');
    $db_query->execute(['current_time' => date('Y/m/d H:i:s')]);

	$isfound = 0;
    while($row = $db_query->fetch())
    {
		echo('<div class="window" onClick="window.location.href = \'?p=set&id='.$row['sid'].'\';">
		<h2 class="window_title">'.$row['title'].'</h2><i style="font-size: 0.6vw; color: gray; margin-left: 5%; margin-top: -0.5vw; display: block;">Kliknij, by przejść do zbioru</i>
		<p style="margin-left: 5%;">
			<i class=\'fas fa-user\'></i>&nbsp;&nbsp;Autor: <b>'.$row['name']." ".$row['surname'].'</b><br />
		</p>
		<br />
		</div>');
		$isfound++;
	}

	if($isfound==0)
	{
		echo("<center>Jeszcze tu niczego nie ma!</center>");
	}
?>
<br />
<br />