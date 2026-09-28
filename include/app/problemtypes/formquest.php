<script src="/include/js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
<script>
	tinymce.init({
      selector: 'textarea#content_area',
      relative_urls : false,
      remove_script_host : false,
      convert_urls : true,
      xss_sanitization: true,
      plugins: 'preview directionality visualblocks visualchars fullscreen link codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount charmap quickbars emoticons',
      menubar: 'file edit view insert format tools table tc help',
      toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | charmap emoticons | preview | insertfile media pageembed template link anchor codesample | showcomments addcomment',
      height: 500,
	  base_url: '/include/js/tinymce',
	  license_key: 'gpl',
	  promotion: false,
	  skin: '<?php echo (isset($settings) && $settings->dark_mode == 0) ? 'oxide' : 'oxide-dark' ?>'
    });
</script>

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

	.tox {
		width: 90%;
		margin-left: 5%;
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
</style>
<div class="window">
	<h2 class="window_title">Treść zadania</h2>
	<iframe src="process.php?r=get_content&mode=pdf&cid=<?php echo(filter_var($_GET['id'], FILTER_VALIDATE_INT)); ?>" style="width: 90%; margin-left: 5%; height: 85vh; border: 0;"></iframe>
	<br />
	<p style="color: white; text-align: center; width: 100%;">To zadanie możesz też otworzyć <a href="process.php?r=get_content&mode=pdf&cid=<?php echo(filter_var($_GET['id'], FILTER_VALIDATE_INT)); ?>" target="_blank">&nbsp;<i class='fas fa-folder-open'></i>&nbsp;tutaj</a>&nbsp;</p>
	<br />
	<br />
</div>
<div class="window">
	<p style="margin-left: 5%;">
		<?php
			$db_query = $pdo->prepare('SELECT COUNT(*) AS count FROM SUBMISSIONS WHERE problem_id=:setid AND user_id=:uid AND mode=1');
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
		<i class='fas fa-info-circle'></i>&emsp;<?php echo($howmanytimes); ?><br /><br />
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