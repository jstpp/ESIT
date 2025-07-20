<?php
	$db_query = $pdo->prepare('SELECT PROBLEMS.PROBLEM_ID AS id, PROBLEMS.type AS type, PROBLEMS.maxpoints AS maxpoints, PROBLEMS.title AS title, PROBLEMS.publish_time AS publish_time, USERS.name AS authorname, USERS.surname AS authorsurname, PROBLEMS.maxattempts AS maxattempts FROM PROBLEMS INNER JOIN USERS ON USERS.USER_ID=PROBLEMS.author_id WHERE PROBLEMS.PROBLEM_ID=:pid');
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
	}

	if($isfound!=1) 
	{ 
		kick();
	}
?>

<center>
	<h1><?php echo($problemtitle); ?></h1>
	<p style="font-weight: normal;"><i class='fas fa-hashtag'></i>&nbsp; ID: <?php echo($problemid); ?> &emsp; <i class='fas fa-user-circle'></i>&nbsp Autor: <?php echo($problemauthor); ?></p>
</center>

<?php 
	if($problemtype==1)
	{
		include(__DIR__.'/../../include/app/examtypes/algquest.php');
	} else if($problemtype==2)
	{
		include(__DIR__.'/../../include/app/examtypes/ctfquest.php');
	} else if($problemtype==3)
	{
		include(__DIR__.'/../../include/app/examtypes/classicquest.php');
	} else if($problemtype==4)
	{
		include(__DIR__.'/../../include/app/examtypes/multiplechoicequest.php');
	} else if($problemtype==5)
	{
		include(__DIR__.'/../../include/app/examtypes/formquest.php');
	} else {
		kick();
	}
?>