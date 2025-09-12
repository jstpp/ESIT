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
        <h1>Dokumenty</h1>
    </div>
    <br style="clear: both;" />
    <div id="content">
        <?php
			$db_query = $pdo->prepare('SELECT * FROM PORTAL_RESOURCES WHERE resource_type="documents"');
			$db_query->execute();

            $count = 0;
			while($row = $db_query->fetch())
			{
                $count++;
                echo('<a style="color: black; text-decoration: none;" target="_blank" href="'.htmlentities($row['resource_path']).'"><div class="card">
                    <h2><i class="fa fa-file-pdf-o"></i>&emsp;'.htmlentities($row['resource_name']).'</h2>
                </div></a>');
			}

            if($count==0) echo('<center><br /><br />Niczego tu jeszcze nie ma!</center>');
		?>
        <br style="clear: both;" />
    </div>
    <br style="clear: both;" />
    <br />
    <?php include(__DIR__.'/../include/portal/main_footer.php'); ?>
</body>
</html>