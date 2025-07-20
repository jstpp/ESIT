<?php
    include(__DIR__.'/../include/config/config_init.php');

    $found = 0;
    try
    {
        $db_query = $pdo->prepare('SELECT * FROM ARTICLES WHERE ID=:id');
        $db_query->execute(['id' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);
    } catch (Exception $e) {
        header("Location: ".$error_link);
    }

    while($row = $db_query->fetch())
	{
        $found++;
        $article_title = $row['title'];
        $article_author = $row['author'];
        $article_time = $row['time'];
        $article_content = $row['content'];
        $article_image_path = $row['image_path'];
    }
    if($found!=1) { header("Location: ".$error_link); }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Bree+Serif&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="include/css/default.css" rel="stylesheet">
    <link href="include/css/main_menu.css" rel="stylesheet">
    <link href="include/css/main_footer.css" rel="stylesheet">
    <link href="include/css/sites/default.css" rel="stylesheet">
    <link href="include/css/sites/style_aktualnosci.css" rel="stylesheet">
    <?php include(__DIR__."/../include/portal/metadata.php"); ?>
</head>
<body>
    <?php include(__DIR__."/../include/portal/main_menu.php"); ?>
    <div id="welcome_sector">
        <img src="<?php echo(htmlentities($article_image_path)); ?>" draggable="false"/>
        <h1><?php echo(htmlentities($article_title)); ?></h1>
    </div>
    <br style="clear: both;" />
    <div id="content" style="padding: 2vmax 2vmax; width: 91%;">
        <div id="metadata" style="user-select: none; color: gray;">
            <p><i class="fa fa-address-book"></i> <?php echo(htmlentities($article_author)); ?>&emsp;<i class="fa fa-clock-o"></i> <?php echo(htmlentities(date("Y-m-d H:i", strtotime($article_time)))); ?></p>
        </div>
        <?php echo($article_content); ?>
    </div>
    <br style="clear: both;" />
    <br />
    <?php include(__DIR__.'/../include/portal/main_footer.php'); ?>
</body>
</html>