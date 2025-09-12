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

		background-color: var(--container-hover-bg);
		transition: 0.2s;
		cursor: default;
		user-select: none;
	}
	.org_user table {
		padding: 0.8vw 0.8vw;
		width: 70%;
		float: left;
	}
	.window .button {
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
	.window .button:hover {
		background-color:rgb(0, 121, 173);
	}
	.window .deletebutton {
		padding: 1vw 1vw;
		float: right;
		text-decoration: none;
		margin-left: 0.5vw;

		background-color:rgb(255, 0, 0);
		color: white;
		border-radius: 5px;
		cursor: pointer;
		transition: 0.2s;
	}
	.window .deletebutton:hover {
		background-color:rgb(148, 0, 0);
	}
	.window .forminput, .window .forminput_a {
		border: 0; 
		padding: 1vw 1.5vw; 
		color: var(--text); 
		background-color: var(--container-hover-bg-textbox); 
		font-family: inherit;
		transition: 0.4s;
		outline: none;
		text-decoration: none;
	}
	.window .forminput_a:hover {
		background-color: var(--container-hover-bg); 
		cursor: pointer;
	}
	.window .window_card
	{
		margin-left: 5%;
		margin-right: 5%;
		background-color: var(--container-hover-bg);
		padding: 1vmax;
		margin-bottom: 0.5vmax;
	}

	.saved_files_table a {
		color: #00b3ff;
		transition: 0.3s;
	}
	.saved_files_table a:hover {
		color: rgb(0, 121, 173);
	}

	input, select {
		font: inherit;
	}
</style>

<center>
	<h1>Ustawienia portalu</h1>
</center>
<div class="window">
	<p>
		<h2 class="window_title">Edytuj aktualności</h2>&emsp;
		<a style="margin-left: 5%; float: left;" class="button" href="?p=addpost">Dodaj post</a>
	</p>
	<br />
	<?php
        $db_query = $pdo->prepare('SELECT * FROM ARTICLES ORDER BY id DESC');
        $db_query->execute();

        while($row = $db_query->fetch())
        {
            $article_id = $row['id'];
            $article_title = $row['title'];
            $article_author = $row['author'];
            $article_time = $row['time'];
            $article_content = $row['content'];
            $article_image_path = $row['image_path'];
			echo('<div class="org_user">
			<table>
				<tr>
					<td><b>'.htmlentities($article_title).'</b> (#'.htmlentities($article_id).')</td>
					<td>'.htmlentities($article_author).'</td>
					<td>'.htmlentities($article_time).'</td>
				</tr>
			</table>
			<a class="deletebutton" href="process.php?r=deletepost&id='.htmlentities($article_id).'">Usuń</a>
			<a class="button" href="?p=modifypost&id='.htmlentities($article_id).'">Modyfikuj</a>
			<a class="button" href="../content.php?id='.htmlentities($article_id).'">Zobacz</a>
			<br style="clear: both;"/>
		</div>');
        }
    ?>
	<br />
	<br />
</div>
<div class="window">
	<h2 class="window_title">Ustawienia ogólne</h2>&emsp;
	<form id="misc_general_form" method="POST" action="process.php?r=modify_resources&mode=general">
		<div class="window_card" style="display: flex; align-items: center;"> 
			<i class="fa fa-keyboard-o" style="font-size: 2vw;"></i>&emsp;<input class="forminput" style="width: 80%" type="text" name="g_title" placeholder="Nazwa strony" value="<?php echo(get_misc_value('general_title')); ?>" />
		</div>
		<div class="window_card" style="display: flex; align-items: center;"> 
			&nbsp;<i class="fa fa-i-cursor" style="font-size: 2vw;"></i>&emsp;&emsp;<input class="forminput" style="width: 80%" type="text" name="g_motd" placeholder="MOTD strony" value="<?php echo(get_misc_value('general_motd')); ?>" />
		</div>
	</form>
	<br />
	<a class="forminput_a" style="float: right; margin-right: 5%;" onClick="document.getElementById('misc_general_form').submit();">Zapisz ustawienia</a>
	<br style="clear: both;" />
	<br />
	<br />
</div>
<div class="window">
	<h2 class="window_title">Terminy wydarzeń</h2>
	<form id="resources_terms_form" method="POST" action="process.php?r=modify_resources&mode=terms" enctype="multipart/form-data">
		<?php
			$db_query = $pdo->prepare('SELECT * FROM TERMS;');
			$db_query->execute();

			$count_terms = 0;
			while($term = $db_query->fetch())
			{
				$count_terms++;
				echo('<div class="window_card window_card_term" id="term_'.$count_terms.'">
				<input type="text" style="width: 40%;" class="forminput" name="term_name_'.$count_terms.'" placeholder="Nazwa etapu" value="'.$term['term_name'].'" />
				<input type="datetime-local" class="forminput" name="term_begin_'.$count_terms.'" placeholder="Początek" value="'.$term['term_begin'].'" />
				<input type="datetime-local" class="forminput" name="term_end_'.$count_terms.'" placeholder="Koniec" value="'.$term['term_end'].'" />');
				if($count_terms!=1)
				{
					echo('<span style="font-size: 2.5vmax; float: right; margin-right: 2vw; cursor: pointer; display: none;" onClick="document.getElementById(\'term_'.($count_terms).'\').remove(); document.getElementById(\'term_'.($count_terms-1).'\').querySelector(\'span\').style.display = \'block\';">×</span>');
				} else {
					echo('<span style="font-size: 2.5vmax; float: right; margin-right: 2vw; cursor: pointer; display: none;">×</span>');
				}
				echo('</div>');
			}
		?>
		<div class="window_card window_card_term" id="term_<?php echo($count_terms+1); ?>">
			<input type="text" style="width: 40%;" class="forminput" name="term_name_<?php echo($count_terms+1); ?>" placeholder="Nazwa etapu" />
			<input type="datetime-local" class="forminput" name="term_begin_<?php echo($count_terms+1); ?>" placeholder="Początek" />
			<input type="datetime-local" class="forminput" name="term_end_<?php echo($count_terms+1); ?>" placeholder="Koniec" />
			<span style="font-size: 2.5vmax; float: right; margin-right: 2vw; cursor: pointer;">×</span>
		</div>
		<script>
			document.querySelector(".window_card_term:last-of-type span").addEventListener("click", function () {
				document.getElementById("term_" + (<?php echo($count_terms+1); ?>)).remove();
				document.getElementById("term_" + (<?php echo($count_terms); ?>)).querySelector("span").style.display = 'block';
			});
		</script>
		<br />
		<a class="forminput_a" style="float: right; margin-right: 5%;" onClick="document.getElementById('resources_terms_form').submit();">Zapisz terminy</a>
		<a class="forminput_a" id="add_btn" onClick="add_term();" style="float: right; margin-right: 0.2vw;"><i class="fa fa-plus"></i>&nbsp;&nbsp;Dodaj nowy</a>
		<br style="clear: both;"/>
		<br />
	</form>
	<script>
		function add_term()
		{
			let sets_count = document.querySelectorAll('.window_card_term').length;
			new_tab = document.querySelector('.window_card_term:last-of-type').cloneNode(true)
			new_tab.id = "term_" + (sets_count+1);
			document.getElementById("term_" + sets_count).querySelector("span").style.display = 'none';

			const term_name = new_tab.querySelector("input[name^='term_name_']");
			const term_begin = new_tab.querySelector("input[name^='term_begin_']");
			const term_end = new_tab.querySelector("input[name^='term_end_']");

			term_name.name = "term_name_" + (sets_count+1);
			term_name.id = "term_name_" + (sets_count+1);
			term_name.value = ""

			term_begin.name = "term_begin_" + (sets_count+1);
			term_begin.id = "term_begin_" + (sets_count+1);
			term_begin.value = "";

			term_end.name = "term_end_" + (sets_count+1);
			term_end.id = "term_end_" + (sets_count+1);
			term_end.value = "";

			const closeBtn = new_tab.querySelector("span");
			closeBtn.onclick = "";
			
			closeBtn.addEventListener("click", function () {
				document.getElementById("term_" + (sets_count+1)).remove();
				document.getElementById("term_" + (sets_count)).querySelector("span").style.display = 'block';
			});

			document.querySelector('.window_card_term:last-of-type').after(new_tab);
		}
	</script>
	<br />
</div>
<div class="window">
	<h2 class="window_title">Media społecznościowe</h2>
	<?php
		$db_query = $pdo->prepare('SELECT * FROM MISC WHERE misc_name LIKE "social_media_%"');
		$db_query->execute();

		while($row = $db_query->fetch())
		{
			if($row['misc_name']=="social_media_yt") $socialmedia_yt = $row;
			if($row['misc_name']=="social_media_ig") $socialmedia_ig = $row;
			if($row['misc_name']=="social_media_fb") $socialmedia_fb = $row;
		}
	?>
	<form id="misc_social_media_form" method="POST" action="process.php?r=modify_resources&mode=socialmedia">
		<div class="window_card" style="display: flex; align-items: center;"> 
			<i class="fa fa-youtube-play" style="font-size: 2vw;"></i>&emsp;<input class="forminput" style="width: 80%" type="text" name="yt_href" placeholder="Link do YoutTube" value="<?php if(isset($socialmedia_yt)) echo($socialmedia_yt['misc_value']); ?>" />
		</div>
		<div class="window_card" style="display: flex; align-items: center;"> 
			<i class="fa fa-instagram" style="font-size: 2vw;"></i>&emsp;&nbsp;&nbsp;<input class="forminput" style="width: 80%" type="text" name="ig_href" placeholder="Link do Instagram" value="<?php if(isset($socialmedia_ig)) echo($socialmedia_ig['misc_value']); ?>" />
		</div>
		<div class="window_card" style="display: flex; align-items: center;"> 
			<i class="fa fa-facebook-square" style="font-size: 2vw;"></i>&emsp;&nbsp;&nbsp;<input class="forminput" style="width: 80%" type="text" name="fb_href" placeholder="Link do Facebook" value="<?php if(isset($socialmedia_fb)) echo($socialmedia_fb['misc_value']); ?>" />
		</div>
	</form>
	<br />
	<a class="forminput_a" style="float: right; margin-right: 5%;" onClick="document.getElementById('misc_social_media_form').submit();">Zapisz ustawienia</a>
	<br style="clear: both;" />
	<br />
</div>
<div class="window">
	<h2 class="window_title">Dokumenty</h2>
	<div style="margin-left: 5%;" class="saved_files_table">
		<h3>Zapisane:</h3>
		<div style="margin-left: 2%;">
			<?php
				$db_query = $pdo->prepare('SELECT * FROM PORTAL_RESOURCES WHERE resource_type="documents"');
				$db_query->execute();

				while($row = $db_query->fetch())
				{
					echo('<p><i class="fa fa-file-pdf-o"></i>&emsp;'.htmlentities($row['resource_name']).'&emsp;<a href="'.htmlentities($row['resource_path']).'" target="_blank">Podejrzyj</a>&emsp;<a href="process.php?r=modify_resources&mode=remove&rid='.$row['RESOURCE_ID'].'">Usuń</a></p>');
				}
			?>
		</div>
		<h3>Do dodania:</h3>
	</div>
	<form id="resources_docs_form" method="POST" action="process.php?r=modify_resources&mode=docs" enctype="multipart/form-data">
		<div class="window_card window_card_document" id="document_1">
			<input class="forminput" type="text" name="document_name_1" placeholder="Nazwa dokumentu" />&emsp;
			<input type="file" name="document_file_1"/>
			<span style="font-size: 2.5vmax; float: right; margin-right: 2vw; cursor: pointer;">×</span>
		</div>
		<a class="forminput_a" style="float: right; margin-right: 5%;" onClick="document.getElementById('resources_docs_form').submit();">Zapisz dokumenty</a>
		<a class="forminput_a" id="add_btn" onClick="add_document();" style="float: right; margin-right: 0.2vw;"><i class="fa fa-plus"></i>&nbsp;&nbsp;Dodaj nowy</a>
		<br />
		<br />
		<br />
		<br />
	</form>
	<script>
		function add_document()
		{
			let docs_count = document.querySelectorAll('.window_card_document').length;
			new_tab_doc = document.querySelector('.window_card_document:last-of-type').cloneNode(true)
			new_tab_doc.id = "document_" + (docs_count+1);
			document.getElementById("document_" + docs_count).querySelector("span").style.display = 'none';

			const document_name = new_tab_doc.querySelector("input[name^='document_name_']");
			const document_file = new_tab_doc.querySelector("input[name^='document_file_']");

			document_name.name = "document_name_" + (docs_count+1);
			document_name.id = "document_name_" + (docs_count+1);
			document_name.value = ""

			document_file.name = "document_file_" + (docs_count+1);
			document_file.id = "document_file_" + (docs_count+1);
			document_file.value = "";

			const closeBtn = new_tab_doc.querySelector("span");
			closeBtn.onclick = "";
			
			closeBtn.addEventListener("click", function () {
				document.getElementById("document_" + (docs_count+1)).remove();
				document.getElementById("document_" + (docs_count)).querySelector("span").style.display = 'block';
			});

			document.querySelector('.window_card_document:last-of-type').after(new_tab_doc);
		}
	</script>
</div>

<div class="window">
	<h2 class="window_title">Zadania pokazowe</h2>
	<div style="margin-left: 5%;" class="saved_files_table">
		<h3>Zapisane:</h3>
		<div style="margin-left: 2%;">
			<?php
				$db_query = $pdo->prepare('SELECT * FROM PORTAL_RESOURCES WHERE resource_type="quests"');
				$db_query->execute();

				while($row = $db_query->fetch())
				{
					if($row['is_actual']==1)
					{
						$r_new = 0;
						$archive_button_text = "Archiwizuj";
					} else {
						$r_new = 1;
						$archive_button_text = "Dodaj do bieżącej edycji";
					}
					echo('<p><i class="fa fa-file-pdf-o"></i>&emsp;'.htmlentities($row['resource_name']).'&emsp;<a href="'.htmlentities($row['resource_path']).'" target="_blank">Podejrzyj</a>&emsp;<a href="process.php?r=modify_resources&mode=remove&rid='.$row['RESOURCE_ID'].'">Usuń</a>&emsp;<a href="process.php?r=modify_resources&mode=archive&rid='.$row['RESOURCE_ID'].'&rnew='.$r_new.'">'.$archive_button_text.'</a></p>');
				}
			?>
	</div>
	<h3>Do dodania:</h3>
	</div>
	<form id="resources_quests_form" method="POST" action="process.php?r=modify_resources&mode=quests" enctype="multipart/form-data">
		<div class="window_card window_card_quest" id="quest_1">
			<input class="forminput" type="text" name="quest_name_1" placeholder="Nazwa zestawu" />&emsp;
			<input type="file" name="quest_file_1"/>
			<span style="font-size: 2.5vmax; float: right; margin-right: 2vw; cursor: pointer;">×</span>
			<p><input type="checkbox" name="is_actual_1" value="1" id="is_actual_1"/>&nbsp;<label for="is_actual">Bieżąca edycja</label></p>
		</div>
		<a class="forminput_a" style="float: right; margin-right: 5%;" onClick="document.getElementById('resources_quests_form').submit();">Zapisz zestawy</a>
		<a class="forminput_a" id="add_btn" onClick="add_quest();" style="float: right; margin-right: 0.2vw;"><i class="fa fa-plus"></i>&nbsp;&nbsp;Dodaj zestaw</a>
	</form>
	<br />
	<br />
	<br />
	<br />
	<script>
		function add_quest()
		{
			let quest_count = document.querySelectorAll('.window_card_quest').length;
			new_tab_q = document.querySelector('.window_card_quest:last-of-type').cloneNode(true)
			new_tab_q.id = "quest_" + (quest_count+1);
			document.getElementById("quest_" + quest_count).querySelector("span").style.display = 'none';

			const quest_name = new_tab_q.querySelector("input[name^='quest_name_']");
			const quest_file = new_tab_q.querySelector("input[name^='quest_file_']");
			const quest_is_actual = new_tab_q.querySelector("input[name^='is_actual_']");

			quest_name.name = "quest_name_" + (quest_count+1);
			quest_name.id = "quest_name_" + (quest_count+1);
			quest_name.value = ""

			quest_file.name = "quest_file_" + (quest_count+1);
			quest_file.id = "quest_file_" + (quest_count+1);
			quest_file.value = "";

			quest_is_actual.name = "is_actual_" + (quest_count+1);
			quest_is_actual.id = "is_actual_" + (quest_count+1);

			const closeBtn = new_tab_q.querySelector("span");
			closeBtn.onclick = "";
			
			closeBtn.addEventListener("click", function () {
				document.getElementById("quest_" + (quest_count+1)).remove();
				document.getElementById("quest_" + (quest_count)).querySelector("span").style.display = 'block';
			});

			document.querySelector('.window_card_quest:last-of-type').after(new_tab_q);
		}
	</script>
</div>
<div class="window">
	<h2 class="window_title">Partnerzy</h2>
	<div style="margin-left: 5%;" class="saved_files_table">
		<h3>Zapisane:</h3>
		<div style="margin-left: 2%;">
			<?php
				$db_query = $pdo->prepare('SELECT * FROM PORTAL_RESOURCES WHERE resource_type="logo"');
				$db_query->execute();

				while($row = $db_query->fetch())
				{
					echo('<p><img src="'.$row['resource_path'].'" style="max-height: 3vw; max-width: 2vw;"/>&emsp;'.htmlentities($row['resource_name']).'&emsp;<a href="'.htmlentities($row['resource_path']).'" target="_blank">Podejrzyj</a>&emsp;<a href="process.php?r=modify_resources&mode=remove&rid='.$row['RESOURCE_ID'].'">Usuń</a></p>');
				}
			?>
		</div>
		<h3>Do dodania:</h3>
	</div>
	<form id="resources_logo_form" method="POST" action="process.php?r=modify_resources&mode=logo" enctype="multipart/form-data">
		<div class="window_card window_card_logo" id="logo_1">
			<input class="forminput" type="text" name="logo_name_1" placeholder="Nazwa partnera" />&emsp;
			<input class="forminput" type="text" name="logo_href_1" placeholder="Link partnera" />&emsp;
			<input type="file" name="logo_file_1"/>
			<span style="font-size: 2.5vmax; float: right; margin-right: 2vw; cursor: pointer;">×</span>
		</div>
		<a class="forminput_a" style="float: right; margin-right: 5%;" onClick="document.getElementById('resources_logo_form').submit();">Zapisz partnerów</a>
		<a class="forminput_a" id="add_btn" onClick="add_logo();" style="float: right; margin-right: 0.2vw;"><i class="fa fa-plus"></i>&nbsp;&nbsp;Dodaj nowego</a>
		<br />
		<br />
		<br />
		<br />
	</form>
	<script>
		function add_logo()
		{
			let logo_count = document.querySelectorAll('.window_card_logo').length;
			new_tab_logo = document.querySelector('.window_card_logo:last-of-type').cloneNode(true)
			new_tab_logo.id = "logo_" + (logo_count+1);
			document.getElementById("logo_" + logo_count).querySelector("span").style.display = 'none';

			const logo_name = new_tab_logo.querySelector("input[name^='logo_name_']");
			const logo_href = new_tab_logo.querySelector("input[name^='logo_href_']");
			const logo_file = new_tab_logo.querySelector("input[name^='logo_file_']");

			logo_name.name = "logo_name_" + (logo_count+1);
			logo_name.id = "logo_name_" + (logo_count+1);
			logo_name.value = "";

			logo_href.name = "logo_href_" + (logo_count+1);
			logo_href.id = "logo_href_" + (logo_count+1);
			logo_href.value = "";

			logo_file.name = "logo_file_" + (logo_count+1);
			logo_file.id = "logo_file_" + (logo_count+1);
			logo_file.value = "";

			const closeBtn = new_tab_logo.querySelector("span");
			closeBtn.onclick = "";
			
			closeBtn.addEventListener("click", function () {
				document.getElementById("logo_" + (logo_count+1)).remove();
				document.getElementById("logo_" + (logo_count)).querySelector("span").style.display = 'block';
			});

			document.querySelector('.window_card_logo:last-of-type').after(new_tab_logo);
		}
	</script>
</div>
<form id="modify_content_form" method="POST" action="process.php?r=modify_content">
	<textarea id="editor_send_content" name="editor_send_content" style="display: none;" readonly></textarea>
</form>
<div class="window">
	<h2 class="window_title">Edytuj stronę główną</h2>
	<div style="width: 40%; margin-left: 5%; float: left;">
		<div id="editor" style="position: relative; height: 60vmin; width: 100%; border-radius: 2vmin;"></div>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/ace.js" type="text/javascript" charset="utf-8"></script>
		<script>
			var i = 0;
			var editor = ace.edit("editor");
			editor.setTheme("ace/theme/<?php echo(str_replace('.css','', $settings->{'code_editor_theme'})); ?>");
			editor.session.setMode("ace/mode/html");

			editor.on("change", function(delta) {
				var code = editor.getValue();
				var iframe = document.getElementById('main_page_content');
				var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

				iframeDoc.open();
				iframeDoc.write(code);
				iframeDoc.close();

				var cssLink = document.createElement('link');

				cssLink.href = "../include/css/sites/style_index.css"; 
				cssLink.rel = "stylesheet"; 
				cssLink.type = "text/css"; 
				iframe.contentDocument.head.appendChild(cssLink);
			});
		</script>
	</div>
	<iframe id="main_page_content" onLoad="if(i===0){main_page_load(this); i = 1;}" src="../include/elements/main_page_content.php" style="background-color: rgb(222, 222, 222); height: 60vmin; width: 48%; margin-right: 5%; float: right; border: 0; border-radius: 1vw;"></iframe>
	<br style="clear: both;" />
	<br />
	<a style="margin-right: 5%;" class="button" onClick="document.getElementById('editor_send_content').innerHTML = editor.getValue(); document.getElementById('modify_content_form').action = document.getElementById('modify_content_form').action+'&page=portal_main'; document.getElementById('modify_content_form').submit();">Zapisz zawartość</a>
	<br style="clear: both;" />
	<br />
</div>
<div class="window">
	<h2 class="window_title">FAQ</h2>
	<div style="width: 40%; margin-left: 5%; float: left;">
		<div id="editor_faq" style="position: relative; height: 60vmin; width: 100%; border-radius: 2vmin;"></div>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/ace.js" type="text/javascript" charset="utf-8"></script>
		<script>
			var j = 0;
			var editor_f = ace.edit("editor_faq");
			editor_f.setTheme("ace/theme/<?php echo(str_replace('.css','', $settings->{'code_editor_theme'})); ?>");
			editor_f.session.setMode("ace/mode/html");

			editor_f.on("change", function(delta) {
				var code = editor_f.getValue();
				var iframe = document.getElementById('faq_content');
				var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

				iframeDoc.open();
				iframeDoc.write(code);
				iframeDoc.close();

				var cssLink = document.createElement('link');

				cssLink.href = "../include/css/sites/style_faq.css"; 
				cssLink.rel = "stylesheet"; 
				cssLink.type = "text/css"; 
				iframe.contentDocument.head.appendChild(cssLink);
			});
		</script>
	</div>
	<iframe id="faq_content" onLoad="if(j===0){faq_load(this); j = 1;}" src="../include/elements/faq_content.php" style="background-color: rgb(222, 222, 222); height: 60vmin; width: 48%; margin-right: 5%; float: right; border: 0; border-radius: 1vw;"></iframe>
	<br style="clear: both;" />
	<br />
	<a style="margin-right: 5%;" class="button" onClick="document.getElementById('editor_send_content').innerHTML = editor_f.getValue(); document.getElementById('modify_content_form').action = document.getElementById('modify_content_form').action+'&page=portal_faq'; document.getElementById('modify_content_form').submit();">Zapisz zawartość</a>
	<br style="clear: both;" />
	<br />
</div>
<div class="window">
	<h2 class="window_title">Dane kontaktowe</h2>
	<div style="width: 40%; margin-left: 5%; float: left;">
		<div id="editor_contact" style="position: relative; height: 60vmin; width: 100%; border-radius: 2vmin;"></div>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/ace.js" type="text/javascript" charset="utf-8"></script>
		<script>
			var k = 0;
			var editor_c = ace.edit("editor_contact");
			editor_c.setTheme("ace/theme/<?php echo(str_replace('.css','', $settings->{'code_editor_theme'})); ?>");
			editor_c.session.setMode("ace/mode/html");

			editor_c.on("change", function(delta) {
				var code = editor_c.getValue();
				var iframe = document.getElementById('contact_content');
				var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

				iframeDoc.open();
				iframeDoc.write(code);
				iframeDoc.close();

				var cssLink = document.createElement('link');

				cssLink.href = "../include/css/sites/style_kontakt.css"; 
				cssLink.rel = "stylesheet"; 
				cssLink.type = "text/css"; 
				iframe.contentDocument.head.appendChild(cssLink);
			});
		</script>
	</div>
	<iframe id="contact_content" onLoad="if(k===0){contact_load(this); k = 1;}" src="../include/elements/contact_info.php" style="background-color: rgb(222, 222, 222); height: 60vmin; width: 48%; margin-right: 5%; float: right; border: 0; border-radius: 1vw;"></iframe>
	<br style="clear: both;" />
	<br />
	<a style="margin-right: 5%;" class="button" onClick="document.getElementById('editor_send_content').innerHTML = editor_c.getValue(); document.getElementById('modify_content_form').action = document.getElementById('modify_content_form').action+'&page=portal_contact'; document.getElementById('modify_content_form').submit();">Zapisz zawartość</a>
	<br style="clear: both;" />
	<br />
</div>
<script>
	function main_page_load(x)
	{
		editor.setValue(x.contentDocument.body.innerHTML, 1);
		var cssLink = document.createElement('link');

		cssLink.href = "../include/css/sites/style_index.css"; 
		cssLink.rel = "stylesheet"; 
		cssLink.type = "text/css"; 
		x.contentDocument.head.appendChild(cssLink);

	}

	function contact_load(x)
	{
		editor_c.setValue(x.contentDocument.body.innerHTML, 1);
		var cssLink = document.createElement('link');

		cssLink.href = "../include/css/sites/style_kontakt.css"; 
		cssLink.rel = "stylesheet"; 
		cssLink.type = "text/css"; 
		x.contentDocument.head.appendChild(cssLink);

	}

	function faq_load(x)
	{
		editor_f.setValue(x.contentDocument.body.innerHTML, 1);
		var cssLink = document.createElement('link');

		cssLink.href = "../include/css/sites/style_faq.css"; 
		cssLink.rel = "stylesheet"; 
		cssLink.type = "text/css"; 
		x.contentDocument.head.appendChild(cssLink);

	}
</script>
<br />
<br />