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
    <link href="include/css/sites/style_zadania.css" rel="stylesheet">
    <?php include(__DIR__."/../include/portal/metadata.php"); ?>
</head>
<body>
    <?php include(__DIR__."/../include/portal/main_menu.php"); ?>
    <div id="welcome_sector">
        <img src="img/background.gif" draggable="false"/>
        <h1>Zadania</h1>
    </div>
    <br style="clear: both;" />
    <div id="content">
        <h2 style="text-align: center; color: rgb(0, 35, 50);">Bieżąca edycja</h2>
        <div class="current_event">
            <?php
                $db_query = $pdo->prepare('SELECT * FROM PORTAL_RESOURCES WHERE resource_type="quests" AND is_actual=1');
                $db_query->execute();

                $count = 0;
                while($row = $db_query->fetch())
                {
                    $count++;
                    echo('<a style="color: white; text-decoration: none;" target="_blank" href="'.htmlentities($row['resource_path']).'">
                        <h2><i class="fa fa-file-pdf-o"></i>&emsp;'.htmlentities($row['resource_name']).'</h2>
                    </a>');
                }

                if($count==0) echo('<center style="width: 100%;"><br /><br />Niczego tu jeszcze nie ma!</center>');
            ?>
        </div>
        <h2 style="text-align: center; color: rgb(0, 35, 50);">Zbiór zadań przygotowawczych</h2>
        <div class="event">
            <?php
                $db_query = $pdo->prepare('SELECT * FROM PORTAL_RESOURCES WHERE resource_type="quests" AND is_actual=0');
                $db_query->execute();

                $count = 0;
                while($row = $db_query->fetch())
                {
                    $count++;
                    echo('<a style="color: white; text-decoration: none;" target="_blank" href="'.htmlentities($row['resource_path']).'">
                        <h2><i class="fa fa-file-pdf-o"></i>&emsp;'.htmlentities($row['resource_name']).'</h2>
                    </a>');
                }

                if($count==0) echo('<center style="width: 100%;"><br />Niczego tu jeszcze nie ma!</center>');
            ?>
        </div>
        <br style="clear: both;" />
        <br />
    </div>
    <br style="clear: both;" />
    <br />
    <?php include(__DIR__.'/../include/portal/main_footer.php'); ?>
</body>
</html>