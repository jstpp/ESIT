<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.3.1/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    tinymce.init({
      selector: 'textarea#content_area',
      images_upload_url: 'process.php?r=getimg',
      relative_urls : false,
      remove_script_host : false,
      convert_urls : true,
      xss_sanitization: true,
      plugins: 'print preview powerpaste directionality advcode visualblocks visualchars fullscreen image link codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount charmap quickbars emoticons',
      menubar: 'file edit view insert format tools table tc help',
      toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | charmap emoticons | preview | insertfile image media pageembed template link anchor codesample | showcomments addcomment',
      height: 500
    });
</script>

<style>
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
		margin-left: 5%;
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
	<h1><?php echo(__("Modify post")); ?></h1>
</center>
<?php
	$db_query = $pdo->prepare('SELECT * FROM ARTICLES WHERE id=:pid');
    $db_query->execute(['pid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);

    while($row = $db_query->fetch())
    {
        $article_id = $row['id'];
        $article_title = $row['title'];
        $article_author = $row['author'];
        $article_time = $row['time'];
        $article_content = $row['content'];
        $article_image_path = $row['image_path'];
    }
?>
<div class="window">
	<form id="new_post" method="POST" action="process.php?r=modifypost&id=<?php echo(filter_var($_GET['id'], FILTER_VALIDATE_INT)); ?>" enctype="multipart/form-data">
		<center>
			<br />
			<label for="fname"><?php echo(__("Title")); ?>:</label>
			<input type="text" id="fname" name="fname" value="<?php echo(htmlspecialchars($article_title, ENT_QUOTES, 'UTF-8')); ?>" style="font-size: 20px; width: 98%; text-align: center;" required><br><br>
			<input type="hidden" name="fareahidden" id="fareahidden" value="none">
		</center>
		<br />
		<textarea id="content_area" required>
			<?php echo($article_content); ?>
		</textarea>
	</form>
	<br />
    <a href="?p=portal"><?php echo(__("Cancel")); ?></a>
	<a onClick="document.getElementById('fareahidden').value = tinymce.activeEditor.getContent(); document.getElementById('new_post').submit();"><?php echo(__("Save")); ?></a>
	<br style="clear: both;"/>
	<br />
</div>
<br />
<br />