<?php
    $db_query = $pdo->prepare('SELECT * FROM MISC WHERE misc_name LIKE "general_%"');
    $db_query->execute();
    while($row = $db_query->fetch())
    {
        if($row['misc_name']=="general_title") $general_title = $row['misc_value'];
        if($row['misc_name']=="general_motd") $general_motd = $row['misc_value'];
    }
?>

<title><?php if(isset($general_title)) { echo($general_title); } else { echo("This is my first ESIT webiste"); } ?></title>

<meta name="description" content="<?php if(isset($general_title)) { echo($general_title); } else { echo("This is my first ESIT webiste"); } ?>. <?php if(isset($general_motd)) { echo($general_motd); } else { echo("This is my first ESIT webiste"); } ?>">
<meta name="viewport" content="width=device-width">

<link rel="icon" href="img/favicon.ico" type="image/x-icon">