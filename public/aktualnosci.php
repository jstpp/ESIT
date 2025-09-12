<?php
    include(__DIR__.'/../include/app/core.php');
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
        <img src="img/background.gif" draggable="false"/>
        <h1>Aktualności</h1>
    </div>
    <br style="clear: both;" />
    <div id="content">
        <?php
            $db_query = $pdo->prepare('SELECT * FROM ARTICLES ORDER BY id DESC');
            $db_query->execute();

            $count = 0;
            while($row = $db_query->fetch())
            {
                $count++;
                $article_id = $row['id'];
                $article_title = $row['title'];
                $article_author = $row['author'];
                $article_time = $row['time'];
                $article_content = $row['content'];
                $article_image_path = $row['image_path'];
                echo('<div class="card" onClick="window.location.href = \'content.php?id='.filter_var($article_id, FILTER_VALIDATE_INT).'\';">
            <img src="'.$article_image_path.'" draggable="false"/>
            <h2>'.htmlspecialchars($article_title).'</h2>
            <p>'.strip_tags(preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '',$article_content)).'</p>
            <a href="content.php?id='.filter_var($article_id, FILTER_VALIDATE_INT).'" class="simple_href">Czytaj więcej ⟶</a>
            <br style="clear: both;" />
        </div>');
            }
            if($count==0) echo('<br /></br /><center>Niczego tu jeszcze nie ma!</center><br />');
        ?>
        <br style="clear: both;" />
    </div>
    <br style="clear: both;" />
    <br />
    <?php include(__DIR__.'/../include/portal/main_footer.php'); ?>
</body>
</html>