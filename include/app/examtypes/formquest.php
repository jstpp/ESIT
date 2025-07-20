<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.3.1/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    tinymce.init({
      selector: 'textarea#content_area',
      relative_urls : false,
      remove_script_host : false,
      convert_urls : true,
      xss_sanitization: true,
      plugins: 'visualblocks visualchars link codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount charmap',
      menubar: 'file edit view insert format tools table tc help',
      toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | charmap emoticons | preview | insertfile image media pageembed template link anchor codesample | showcomments addcomment',
      height: 500
    });
</script>

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

	.tox {
		width: 90%;
		margin-left: 5%;
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
</style>

<div class="window">
	<h2 class="window_title">Treść zadania</h2>
	<iframe src="content/quests/<?php echo($problemid); ?>/pdf/<?php echo($problemid); ?>.pdf" style="width: 90%; margin-left: 5%; height: 85vh; border: 0;"></iframe>
	<br />
	<p style="color: white; text-align: center; width: 100%;">To zadanie możesz też otworzyć <a href="content/quests/q1.pdf" target="_blank">&nbsp;<i class='fas fa-folder-open'></i>&nbsp;tutaj</a>&nbsp;</p>
	<br />
	<br />
</div>
<div class="window">
	<p style="margin-left: 5%;">
		<i class='fas fa-info-circle'></i>&emsp;Rozwiązanie do tego zadania możesz wysłać jeszcze <b>7 razy</b>.<br />
		<i class='fas fa-clock'></i>&emsp;Test sprawdzany przez komisję. Wyniki pojawią się dopiero po pewnym czasie.
	</p>
</div>
<div class="window" id="mysolution">
	<form id="my_anwser_form" method="POST" action="process.php?r=save_form&id=<?php echo($problemid); ?>">
		<h2 style="margin-left: 5%;">Twoja odpowiedź</h2>
		<textarea id="content_area" required>
		</textarea>
		<input type="hidden" name="fareahidden" id="fareahidden" value="none">
		<br />
	</form>
	<a class="forminput" style="font-weight: normal; float: right; margin-right: 5%;"onClick="document.getElementById('fareahidden').value = tinymce.activeEditor.getContent(); document.getElementById('my_anwser_form').submit();">Wyślij odpowiedź</a>
	<br style="clear: both;"/>
	<br />
</div>
<br />
<br />