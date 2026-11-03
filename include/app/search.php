<?php
	$key = (!isset($_GET['key']) || strlen($_GET['key'])<1) ? null : $_GET['key'];
?>

<style>
	.content_window {
		width: 95%;
		margin-left: 2.5%;
		margin-top: 1vw;

		background-color: var(--container-bg);

		box-shadow: 0 0 0.1vmax 0.2vmax var(--container-hover-bg);
		border-radius: 1vw;
		transition: 0.2s;
		user-select: none;
		cursor: pointer;

		display: flex;
		align-content: stretch;
		overflow: hidden;
	}
	.content_window:hover {
		background-color: var(--container-hover-bg);
	}

	.content_window .window_title {
		margin-left: 5%;
		margin-top: 1.5vw;
	}

	.search_box_rounded_input_box {
		width: 70%;
		overflow: hidden;
		height: fit-content;
		background-color: var(--container-bg);
		color: var(--text);
		border-radius: 1.5vmax;
		display: flex;
		box-shadow: 0 0 0.5vmax 0.3vmax var(--container-hover-bg);
		transition: 0.3s;
	}
	.search_box_rounded_input_box input[type="text"] {
		height: 100%;
		flex-grow: 5;
		padding: 0.5vmax 1vmax;
		background-color: transparent;
		font-family: inherit;
		color: inherit;
		font-size: 1vmax;
		outline: none;
		border: none;
		margin-top: 0.3vmax;
	}

	.search_box_rounded_input_box input[type="submit"] {
		width: fit-content;
		border-radius: 1vmax;
		padding: 0.5vmax 2vmax;
		color: var(--text);
		font-family: inherit;
		font-size: 1vmax;
		font-weight: bold;
		margin: 0.3vmax;
		border: none;
		outline: none;
		background-color: var(--highlight-color);
		transition: 0.3s;
		cursor: pointer;
	}
	.search_box_rounded_input_box:hover {
		box-shadow: 0 0 0.3vmax 0.2vmax var(--highlight-color);
	}
	.search_box_rounded_input_box input[type="submit"]:hover {
		padding: 0.5vmax 2.5vmax;
		opacity: 0.8;
	}

	#search_box_result_container {
		position: absolute;
		float: left;
		background: rgba(0,0,0,0.5);
		width: calc(82vw * 0.7 - 2vmax);
		height: fit-content;
		margin-left: calc(82vw * 0.15);
		margin-top: 0.5vmax;
		padding: 1vmax;
		border-radius: 1.5vmax;
		z-index: 10;
	}
	#search_box_result_container a {
		padding: 1vmax;
		background-color: var(--bg);
		color: var(--text);
		text-decoration: none;
		transition: 0.3s;
		height: fit-content;
		display: block;
		margin-top: 0.2vmax;
		border-radius: 0.3vmax;
	}
	#search_box_result_container a:hover {
		background-color: var(--container-bg);
	}
</style>

<center>
	<h1><?php echo(__("Content center")); ?></h1> 
	<div id="search_box">
		<form class="search_box_rounded_input_box" method="GET" action="index.php">
			<input type="hidden" name="p" value="search">
			<input type="text" id="search_box_key" name="key" placeholder="<?php echo(__("What are you looking for?")); ?>" oninput="search(document.getElementById('search_box_key').value);"/>
			<input type="submit" value="<?php echo(__("Search")); ?>"/>
		</form>	
		<div id="search_box_result_container" style="display: none;">
			<div id="search_box_result_container_channels" style="display: none;">
				<br /><b><?php echo(__("Channels")); ?></b>
				<div id="search_box_result_container_channels_content">
				</div>
			</div>
			<div id="search_box_result_container_contentsets" style="display: none;">
				<br /><b><?php echo(__("Contentsets")); ?></b>
				<div id="search_box_result_container_contentsets_content"> 
				</div>
			</div>
			<div id="search_box_result_container_content" style="display: none;">
				<br /><b><?php echo(__("Content")); ?></b>
				<div id="search_box_result_container_content_content"> 
				</div>
			</div>
		</div>
		<br />
		<br />
		<script>
			let timeout;
			
			async function search(key)
			{
				let data = await fetch('process.php?r=search&key=' + encodeURIComponent(key))
					.then(r => r.json());
				
				let e = 0;

				document.getElementById('search_box_result_container_channels_content').innerHTML = '';
				document.getElementById('search_box_result_container_contentsets_content').innerHTML = '';
				document.getElementById('search_box_result_container_content_content').innerHTML = '';

				let channels_length = (data.channels.length>3) ? 3 : data.channels.length;
				let contentsets_length = (data.contentsets.length>3) ? 3 : data.contentsets.length;
				let content_length = (data.content.length>3) ? 3 : data.content.length;

				if(channels_length>0) {
					document.getElementById('search_box_result_container_channels').style.display = 'block';
					for (let i = 0; i < channels_length; i++)
					{
						document.getElementById('search_box_result_container_channels_content').innerHTML = 
						document.getElementById('search_box_result_container_channels_content').innerHTML +
						'<a href="index.php?p=channel&id='+data.channels[i].CHANNEL_ID+'">' + data.channels[i].title +  '</a>';
					}
					e++;
				} else {
					document.getElementById('search_box_result_container_channels').style.display = 'none';
				}
				if(contentsets_length>0) {
					document.getElementById('search_box_result_container_contentsets').style.display = 'block';
					for (let i = 0; i < contentsets_length; i++)
					{
						document.getElementById('search_box_result_container_contentsets_content').innerHTML = 
						document.getElementById('search_box_result_container_contentsets_content').innerHTML +
						'<a href="index.php?p=channel&id='+data.contentsets[i].channel_id+'">' + data.contentsets[i].title +  '</a>';
					}
					e++;
				} else {
					document.getElementById('search_box_result_container_contentsets').style.display = 'none';
				}
				if(content_length>0) {
					document.getElementById('search_box_result_container_content').style.display = 'block';
					for (let i = 0; i < content_length; i++)
					{
						document.getElementById('search_box_result_container_content_content').innerHTML = 
						document.getElementById('search_box_result_container_content_content').innerHTML +
						'<a href="index.php?p=content&id='+data.content[i].CONTENT_ID+'">' + data.content[i].title +  '</a>';
					}
					e++;
				} else {
					document.getElementById('search_box_result_container_content').style.display = 'none';
				}
				
				if(e>0) {
					document.getElementById('search_box_result_container').style.display = 'block';
				} else {
					document.getElementById('search_box_result_container').style.display = 'none';
				}
			}
		</script>
	</div>
	<?php if($key) echo('<p>'.__("Displaying results for").'&nbsp;&nbsp;<code style="color: var(--bg); background-color: var(--text);">&nbsp;'.htmlentities($key)."&nbsp;</code></p>"); ?>
	<br style="clear: both;" />
</center>
<?php
	$db_query = $pdo->prepare('SELECT CHANNELS.CHANNEL_ID AS sid, CHANNELS.img_path AS img_path, CHANNELS.isarchived AS isarchived, CHANNELS.title AS title, USERS.name AS name, USERS.surname AS surname FROM CHANNELS INNER JOIN USERS ON CHANNELS.author_id=USERS.USER_ID WHERE :current_time>=CHANNELS.publish_time AND CHANNELS.title LIKE :key ORDER BY CHANNELS.isarchived ASC, CHANNELS.publish_time DESC');
    $db_query->execute(['current_time' => date('Y/m/d H:i:s'), 'key' => '%'.$key.'%' ?? '%']);

	$isfound_channels = 0;
    while($row = $db_query->fetch())
    {
		if($isfound_channels==0) echo("<h2 style='margin-left: 2.5%;'>".__("Channels")."</h2>");
		echo('<div class="content_window" onClick="window.location.href = \'?p=channel&id='.$row['sid'].'\';">
		<div style="flex-grow: 5;">
		<h2 class="window_title">'.$row['title']);
		if($row['isarchived']==1) echo("&emsp;<span style='background-color: #d1b502; padding: 0.5vmax; border-radius: 10px;'>".__("Archived")."</span>");
		echo('</h2><i style="font-size: 0.6vw; color: gray; margin-left: 5%; margin-top: -0.5vw; display: block;">'.__("Click to enter the channel").'</i>
		<p style="margin-left: 5%;">
			<i class=\'fas fa-user\'></i>&nbsp;&nbsp;'.__("Author").': <b>'.$row['name']." ".$row['surname'].'</b><br />
		</p>
		<br />
		</div>
		<div style="width: 30%; mask-image: linear-gradient(to left, black 0%, black 60%, transparent 100%); background: url(\''.$row['img_path'].'\'); background-size: cover;"></div>
		</div>');
		$isfound_channels++;
	}

	$db_query = $pdo->prepare('SELECT CONTENT.CONTENT_ID AS sid, CONTENT.isarchived AS isarchived, CONTENT.title AS title, USERS.name AS name, USERS.surname AS surname FROM CONTENT INNER JOIN USERS ON CONTENT.author_id=USERS.USER_ID WHERE :current_time>=CONTENT.publish_time AND CONTENT.title LIKE :key ORDER BY CONTENT.isarchived ASC, CONTENT.publish_time DESC');
    $db_query->execute(['current_time' => date('Y/m/d H:i:s'), 'key' => '%'.$key.'%' ?? '%']);

	$isfound_content = 0;
    while($row = $db_query->fetch())
    {
		if($isfound_content==0) echo("<h2 style='margin-left: 2.5%;'>".__("Content")."</h2>");
		echo('<div class="content_window" onClick="window.location.href = \'?p=content&id='.$row['sid'].'\';">
		<div style="flex-grow: 5;">
		<h2 class="window_title">'.$row['title']);
		if($row['isarchived']==1) echo("&emsp;<span style='background-color: #d1b502; padding: 0.5vmax; border-radius: 10px;'>".__("Archived")."</span>");
		echo('</h2><i style="font-size: 0.6vw; color: gray; margin-left: 5%; margin-top: -0.5vw; display: block;">'.__("Click to see the content").'</i>
		<p style="margin-left: 5%;">
			<i class=\'fas fa-user\'></i>&nbsp;&nbsp;'.__("Author").': <b>'.$row['name']." ".$row['surname'].'</b><br />
		</p>
		<br />
		</div>
		</div>');
		$isfound_content++;
	}

	$db_query = $pdo->prepare('SELECT PROBLEMSETS.channel_id AS sid, PROBLEMSETS.title AS title, USERS.name AS name, USERS.surname AS surname FROM PROBLEMSETS INNER JOIN USERS ON PROBLEMSETS.author_id=USERS.USER_ID WHERE :current_time>=PROBLEMSETS.publish_time AND PROBLEMSETS.title LIKE :key ORDER BY PROBLEMSETS.publish_time DESC');
    $db_query->execute(['current_time' => date('Y/m/d H:i:s'), 'key' => '%'.$key.'%' ?? '%']);

	$isfound_contentsets = 0;
    while($row = $db_query->fetch())
    {
		if($isfound_contentsets==0) echo("<h2 style='margin-left: 2.5%;'>".__("Contentsets")."</h2>");
		echo('<div class="content_window" onClick="window.location.href = \'?p=channel&id='.$row['sid'].'\';">
		<div style="flex-grow: 5;">
		<h2 class="window_title">'.$row['title']);
		echo('</h2><i style="font-size: 0.6vw; color: gray; margin-left: 5%; margin-top: -0.5vw; display: block;">'.__("Click to enter the channel").'</i>
		<p style="margin-left: 5%;">
			<i class=\'fas fa-user\'></i>&nbsp;&nbsp;'.__("Author").': <b>'.$row['name']." ".$row['surname'].'</b><br />
		</p>
		<br />
		</div>
		</div>');
		$isfound_contentsets++;
	}

	if(($isfound_channels + $isfound_content + $isfound_contentsets)==0)
	{
		echo('
		<div style="margin-left: auto; margin-right: auto; margin-top: 5vmax; text-align: center; display: flex; flex-direction: column; justify-content: cetner; width: 30%; padding: 3vmax; background-color: var(--container-bg); box-shadow: 0 0 0.1vmax 0.2vmax var(--container-hover-bg); border-radius: 1vw;" >
			<i class="fa fa-hourglass-3" style="font-size: 7vmax; margin: auto;"></i>
			<center style="margin-top: 2vmax; user-select: none;"><i>'.__("There's nothing here yet!").'</i></center>
		</div>
		');
	}
?>
<br />
<br />