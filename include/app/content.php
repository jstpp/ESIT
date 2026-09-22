<?php
	$db_query = $pdo->prepare('SELECT CONTENT.CONTENT_ID AS id, CONTENT.type AS type, CONTENT.problemset AS problemset, CONTENT.maxpoints AS maxpoints, CONTENT.title AS title, CONTENT.stars AS stars, CONTENT.publish_time AS publish_time, USERS.name AS authorname, USERS.surname AS authorsurname, CONTENT.maxattempts AS maxattempts FROM CONTENT INNER JOIN USERS ON USERS.USER_ID=CONTENT.author_id WHERE CONTENT.CONTENT_ID=:pid');
    $db_query->execute(['pid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);
	$isfound = 0;
    while($row = $db_query->fetch())
    {
		$isfound++;
		$problemtitle = $row['title'];
		$problemid = $row['id'];
		$publishtime = $row['publish_time'];
		$problemauthor = $row['authorname']." ".$row['authorsurname'];
		$maxattempts = $row['maxattempts'];
		$maxpoints = $row['maxpoints'];
		$problemtype = $row['type'];
		$problemset = $row['problemset'];
		$problemstars = $row['stars'];
	}

	if($isfound!=1 or (strtotime($publishtime)>strtotime("now") and !has_permission('main.display.all_resources')) or (!check_problemset_availability($problemset, $pdo) and !has_permission('main.display.all_resources'))) 
	{ 
		kick();
	}
?>

<style>
	.star_box {
		position: absolute; 
		right: 1.5vmax; 
		margin-top: 1vmax; 
		font-size: 1vmax;
		padding: 0.5vmax;
		cursor: pointer;
		transition: 0.3s;
		border-radius: 0.5vmax;
		background-color: var(--text);
		color: var(--bg);
		text-decoration: none;
	}
	.star_box:hover {
		color: var(--text);
		background: transparent;
	}
</style>

<a href="process.php?r=star_content&id=<?php echo($problemid); ?>" class="star_box">
	<i class="far fa-star"></i>&nbsp;<?php echo((check_star($_GET['id'], $_SESSION['AUTH_ID'])) ? __('Unstar') : __('Star')); ?>&nbsp;
</a>

<center>
	<h1><?php echo($problemtitle); ?></h1>
	<p style="font-weight: normal;"><i class='fas fa-hashtag'></i>&nbsp; ID: <?php echo($problemid); ?> &emsp; <i class='fas fa-user-circle'></i>&nbsp; <?php echo(__("Author")); ?>: <?php echo($problemauthor); ?>&emsp;<i class="far fa-star"></i>&nbsp; <?php echo($problemstars); ?></p>
</center>

<?php 
	if($problemtype==1)
	{
		include(__DIR__.'/../../include/app/problemtypes/algquest.php');
	} else if($problemtype==2)
	{
		include(__DIR__.'/../../include/app/problemtypes/ctfquest.php');
	} else if($problemtype==3)
	{
		include(__DIR__.'/../../include/app/problemtypes/classicquest.php');
	} else if($problemtype==4)
	{
		include(__DIR__.'/../../include/app/problemtypes/multiplechoicequest.php');
	} else if($problemtype==5)
	{
		include(__DIR__.'/../../include/app/problemtypes/formquest.php');
	} else {
		kick();
	}
?>