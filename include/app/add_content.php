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
	.window table {
		width: 90%;
		margin-left: 5%;
		user-select: none;
	}
	.window table a {
		text-decoration: none;
		color: var(--highlight-color);
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

	.content_type_select_flexbox_option {
		padding: 1vmax;
		flex-grow: 1;
		width: 33%;
		height: 10vmax;
		transition: 0.3s;
		cursor: pointer;
		overflow: hidden;
		border-radius: 0.5vmax;
	}
	.content_type_select_flexbox_option * {
		user-select: none;
	}
	.content_type_select_flexbox_option i {
		font-size: 8vmax;
	}
	.content_type_select_flexbox_option:hover {
		filter: brightness(70%);
	}
</style>
<br />
<br />
<div id="content_type_select">
	<div class="window" id="content_type_select_window">
		<h2 style="margin-left: 2.5%;"><?php echo(__("Choose content type")); ?></h1>
		<div id="content_type_select_flexbox" style="margin: 2.5%; display: flex; flex-wrap: wrap; gap: 0.5vmax;">
			<div class="content_type_select_flexbox_option" onClick="validate_data(); typeSelect(6);" style="background-color: <?php print(content_type_identification('vid')['color']); ?>;">
				<h3><?php print(content_type_identification('vid')['full_name']); ?></h3><br />
				<i class="<?php print(content_type_identification('vid')['icon']); ?>" style="color: rgba(0, 0, 0, 0.2);"></i>
			</div>
		</div>
	</div>
</div>
<div id="content_add_window" style="display: none;">
	<center>
		<h1><?php echo(__("New content")); ?> (<?php echo(__("in")); ?>: <?php echo(htmlentities($settitle)); ?>)</h1>
	</center>
	<br />
	<br />
	<form method="POST" action="process.php?r=add_content&sid=<?php echo(htmlentities($setid)); ?>" enctype="multipart/form-data" id="newcontentform">
		<div class="window" id="main_info">
			<h2 class="window_title" style="margin-left: 2.5%;"><?php echo(__("Basic information")); ?></h2>
			<input type="text" name="content_title" onChange="validate_data();" placeholder="<?php echo(__("Content title")); ?>" class="forminput" style="margin-left: 2.5%; width: 91.75%;" required />
			<br />
			<input type="hidden" id="content_type" name="content_type" value="0" />
			<br />
			<select class="forminput" name="content_isarchived" onChange="validate_data();" style="margin-right: 2.5%; width: 95%; float: right;" required>
				<option value="0"><?php echo(__("Public")); ?></option>
				<option value="1"><?php echo(__("Hidden/archived")); ?></option>
			</select>
			<br style="clear: both;"/>
			<br />
			<p style="margin-left: 2.5%; width: 40%;"><?php echo(__("Publishing time")); ?>:&emsp;<input type="datetime-local" onChange="validate_data();" class="forminput" name="publish_time" /></p>
			<br />
		</div>
		<div class="window" id="vid_video">
			<h2 class="window_title" style="margin-left: 2.5%;"><?php echo(__("Video")); ?></h2>
			<input type="file" style="margin-left: 2.5%;" name="vid_file" accept=".mp4"/>
			<p style="margin-left: 2.5%;"><?php echo(__("The file should be in .mp4 format")); ?>.</p>
			<br />
		</div>
	</form>
	<div class="window">
		<br />
		<a class="forminput_a" id="button_1" onClick="document.getElementById('newcontentform').submit();" style="display: none; float: right; margin-right: 2.5%;"><i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo(__("Add new content")); ?></a>
		<a class="forminput_a" href="index.php?p=channel&id=<?php echo($setid); ?>" style="float: right; margin-right: 2.5%;"><i class="fa fa-close"></i>&nbsp;&nbsp;<?php echo(__("Cancel")); ?></a>
		<br style="clear: both;" />
		<br />
		<fieldset id="r_i_validation" style="margin-top: 2vmax; width: 92.5%; margin: auto;">
			<legend><?php echo(__("Input validation")); ?></legend>
			<p class="data-invalid" id="c_1"><?php echo(__("Fill all required textboxes.")); ?></p>
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
						if(document.querySelector('input[name="content_type"]').value>0 
						&& document.querySelector('input[name="publish_time"]').value.length>0
						&& document.querySelector('input[name="content_title"]').value.length>0 
						&& document.querySelector('select[name="content_isarchived"]').value.length>0) 
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
	<script>
		function typeSelect(x)
		{
			if(parseInt(x)===6)
			{
				document.getElementById('content_type').value = 6;
				document.getElementById('vid_video').style.display = 'block';
			} else {
				return -1;
			}

			document.getElementById('content_add_window').style.display = 'block';
			document.getElementById('content_type_select').style.display = 'none';
		}
		typeSelect(document.getElementById('type_select'));
	</script>
</div>
<br style="clear: both;" />
<br />