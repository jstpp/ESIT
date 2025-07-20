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
		padding: 0.75vw 1.25vw; 
		color: #dae2e6; 
		background-color: #2a2c2e; 
		font-family: inherit;
		outline: none;
	}
	.window .forminput_a {
		border: 0; 
		padding: 0.75vw 2.5%; 
		color: #dae2e6; 
		background-color: #2a2c2e; 
		font-family: inherit;
		cursor: pointer;
		transition: 0.4s;
		text-decoration: none;
	}
	.window .forminput_a:hover {
		background-color: #3e4145;
	}

	.window input, .window select {
		font: inherit;
	}
</style>
<div class="window">
	<p style="margin-left: 5%;">
		<i class='fas fa-info-circle'></i>&emsp;Rozwiązanie do tego zadania możesz wysłać jeszcze <b>7 razy</b>.<br />
	</p>
</div>
<div class="window">
	<br />
	<br />
	<center>
		<a class="forminput_a" href="content/ctf_public/<?php echo($problemid); ?>/<?php echo(scandir("content/ctf_public/".$problemid, SCANDIR_SORT_DESCENDING)[0]); ?>" download>Pobierz plik</a>
	</center>
	<br />
	<br />
</div>
<div class="window">
	<h2 style="margin-left: 5%;">Twoja odpowiedź</h2>
	<form id="ctf_form" method="POST" action="process.php?r=verify_ctf&id=<?php echo($problemid); ?>">
		<input type="text" name="ctf_flag" id="ctf_flag" class="forminput" style="margin-left: 5%; width: 85%;" placeholder="$_{TWOJA_FLAGA}_$"/>
		<br />
		<br />
		<a class="forminput_a" onClick="document.getElementById('ctf_form').submit();" style="font-weight: normal; float: right; margin-right: 6.75%;">Zweryfikuj odpowiedź</a>
	</form>
	<br style="clear: both;" />
	<br />
	<br />
</div>
<br />
<br />