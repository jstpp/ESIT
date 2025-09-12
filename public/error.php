<?php
    include(__DIR__.'/../include/app/core.php');
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <?php include(__DIR__."/../include/portal/metadata.php"); ?>
    <link href="https://fonts.googleapis.com/css2?family=Bree+Serif&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="include/css/default.css" rel="stylesheet">
    <link href="include/css/main_menu.css" rel="stylesheet">
    <link href="include/css/main_footer.css" rel="stylesheet">
    <link href="include/css/sites/style_index.css" rel="stylesheet">
</head>
<body>
    <?php include(__DIR__."/../include/portal/main_menu.php"); ?>
    <div id="welcome_sector" style="height: 100vh;">
        <h1>Wystąpił błąd!</h1>
        <h2 id="welcome_motd">Administratorzy pewnie się cieszą...</h2>
        <br style="clear: both;" />
    </div>
</body>
</html>