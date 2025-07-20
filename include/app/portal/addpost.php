<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.3.1/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    tinymce.init({
      selector: 'textarea#content_area',
      images_upload_url: 'process.php?r=getimg',
      relative_urls : false,
      remove_script_host : false,
      convert_urls : true,
      xss_sanitization: true,
      plugins: 'print preview powerpaste directionality advcode visualblocks visualchars fullscreen image link codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists checklist wordcount textpattern pageembed charmap tinycomments quickbars linkchecker emoticons advtable',
      menubar: 'file edit view insert format tools table tc help',
      toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | charmap emoticons | preview | insertfile image media pageembed template link anchor codesample | showcomments addcomment',
      height: 500
    });
</script>

<style>
	.window {
		width: 93%;
		margin-left: 2.5%;
		margin-top: 1vw;
        padding: 1% 1%;

		background-color: #313136;

		border: 0.2vw solid #2a2c2e;
		border-radius: 1vw;
	}

	.window .window_title {
		margin-left: 5%;
		margin-top: 1.5vw;
	}

	.set_link {
		padding: 1vw 1vw;
		width: 90%;
		margin-left: 4%;
		
		display: flex;

		font-weight: bold;
		color: inherit;
		text-decoration: none;
		user-select: none;
		transition: 0.2s;
		cursor: pointer;
	}
	.set_link:hover {
		background-color: #2a2c2e;
	}
	
	.window .news {
		background-color: #2a2c2e;
		width: 85%;
		margin-left: 5%;
		margin-top: 0.5vw;
		padding: 1% 2%;
	}

	.portal_articles_card {
		margin_left: 5%;
	}

	.org_user {
		padding: 1vw 1vw;
		margin-left: 5%;
		margin-top: 0.5vw;
		width: 87%;

		background-color: #2a2c2e;
		transition: 0.2s;
		cursor: default;
		user-select: none;
	}
	.org_user:hover {
		background-color: #3e4145;
	}
	.org_user table {
		padding: 0.8vw 0.8vw;
		width: 70%;
		float: left;
	}
	.window a {
		padding: 1vw 1vw;
		float: right;
		text-decoration: none;
		margin-left: 0.5vw;

		background-color: #00b3ff;
		color: white;
		border-radius: 5px;
		cursor: pointer;
		transition: 0.2s;
	}
	.window a:hover {
		background-color: #6ed4ff;
	}
</style>

<center>
	<h1>Nowy post</h1>
</center>
<div class="window">
	<form id="new_post" method="POST" action="process.php?r=addpost" enctype="multipart/form-data">
		<center>
			<br />
			<label for="fname">Nazwa posta:</label>
			<input type="text" id="fname" name="fname" style="font-size: 20px; width: 98%; text-align: center;" required><br><br>
			<label for="fimage">Okładka posta: &nbsp;</label>
			<input type="file" id="fimage" name="fimage" style="text-align: center;" required>
			<input type="hidden" name="fareahidden" id="fareahidden" value="none">
		</center>
		<br />
		<p style="text-align: center;"><i>Zalecane jest nieustawianie konkretnej czcionki w tekście - w poście widać wówczas czcionkę domyślną dla Portalu.</p></i>
		<textarea id="content_area" required>
		</textarea>
	</form>
	<br />
    <a href="?p=portal">Anuluj</a>
	<a onClick="document.getElementById('fareahidden').value = tinymce.activeEditor.getContent(); document.getElementById('new_post').submit();">Opublikuj</a>
	<br style="clear: both;"/>
	<br />
</div>
<br />
<br />