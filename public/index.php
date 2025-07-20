<?php
    include(__DIR__.'/../include/config/config_init.php');
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
    <div id="welcome_sector">
        <h1 style="width: 80%; margin-left: 10%;"><?php if(isset($general_title)) { echo($general_title); } else { echo("This is my first ESIT website!"); } ?></h1>
        <h2 id="welcome_motd"></h2>
        <br style="clear: both;" />
    </div>
    <br style="clear: both;" />
    <div id="news_sector">
        <h1 style="text-align: center;">Aktualności</h1>
        <div id="articles_list">
            <?php
                $db_query = $pdo->prepare('SELECT * FROM ARTICLES ORDER BY id DESC LIMIT 4');
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
                <img src="'.$article_image_path.'" draggable="false" alt="'.htmlspecialchars($article_title).'"/>
                <h2>'.htmlspecialchars($article_title).'</h2>
                <p>'.strip_tags(preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '',$article_content)).'</p>
                <a href="content.php?id='.filter_var($article_id, FILTER_VALIDATE_INT).'" class="simple_href">Czytaj więcej ⟶</a>
                <br style="clear: both;" />
            </div>');
                }
                if($count==0) echo('<center style="width: 100%;">Tu będą pojawiać się Twoje aktualności!</center>');
            ?>
        </div>
        <center>
            <a href="aktualnosci.php" class="simple_href">Więcej aktualności ⟶</a>
            <br />
            <br />
        </center>
    </div>
    <br style="clear: both;" />
    <br />
    <br />
    <div id="partners_sector">
        <div class="partners_slide">
        <?php
            $db_query = $pdo->prepare('SELECT * FROM PORTAL_RESOURCES WHERE resource_type="logo"');
            $db_query->execute();

            $count = 0;
            while($row = $db_query->fetch())
            {
                $count++;
                echo('<a href="'.$row['resource_comment'].'"><img src="'.$row['resource_path'].'" alt="'.$row['resource_name'].'"/></a>');
            }
        ?>
        </div>
        <script>document.querySelector('.partners_slide:last-of-type').after(document.querySelector('.partners_slide:last-of-type').cloneNode(true));</script>
    </div>
    <br style="clear: both;" />
    <br />
    <br />
    <br />
    <div id="big_box_1" style="display: flex;">
        <div id="timetable_sector">
            <h1 style="font-size: 3vmax;">Kalendarium wydarzenia</h1>
            <?php
                $db_query = $pdo->prepare('SELECT * FROM TERMS ORDER BY term_begin');
                $db_query->execute();

                $count = 0;
                while($row = $db_query->fetch())
                {
                    $count++;
                    if(strtotime($row['term_end'])<time())
                    {
                        if(date('d.m.Y', strtotime($row['term_begin']))==date('d.m.Y', strtotime($row['term_end'])))
                        {
                            echo('<h3 style="color: gray; border-color: gray;"><span class="timetable_date">'.date('d.m.Y', strtotime($row['term_begin'])).'</span><br />'.$row['term_name'].'</h3>');
                        } else {
                            echo('<h3 style="color: gray; border-color: gray;"><span class="timetable_date">'.date('d.m.Y', strtotime($row['term_begin'])).' - '.date('d.m.Y', strtotime($row['term_end'])).'</span><br />'.$row['term_name'].'</h3>');
                        }
                    } else {
                        if(date('d.m.Y', strtotime($row['term_begin']))==date('d.m.Y', strtotime($row['term_end'])))
                        {
                            echo('<h3><span class="timetable_date">'.date('d.m.Y', strtotime($row['term_begin'])).'</span><br />'.$row['term_name'].'</h3>');
                        } else {
                            echo('<h3><span class="timetable_date">'.date('d.m.Y', strtotime($row['term_begin'])).' - '.date('d.m.Y', strtotime($row['term_end'])).'</span><br />'.$row['term_name'].'</h3>');
                        }
                    } #IDEA: "current" class for current events
                }
                
                if($count==0) echo('<center><br /><br />Niczego tu jeszcze nie ma!</center>');
            ?>
            <br />
        </div>
        <div id="documents_sector">
            <div>
                <i class="fa fa-book" style="font-size: 10vmax; color: rgba(88, 115, 126, 0.4)"></i>
                <h1 style="font-size: 3vmax;">Przydatne dokumenty</h1>
                <?php
                    $db_query = $pdo->prepare('SELECT * FROM PORTAL_RESOURCES WHERE resource_type="documents" AND is_actual=1 ORDER BY resource_name');
                    $db_query->execute();

                    $count = 0;
                    while($row = $db_query->fetch())
                    {
                        echo('<a href="'.$row['resource_path'].'" class="simple_href">'.$row['resource_name'].' ⟶</a><br />');
                        $count++;
                    }
                    
                    if($count==0) echo('<center>Niczego tu jeszcze nie ma!</center>');
                ?>
            </div>
        </div>
    </div>
    <div>
    <?php
        include('include/elements/main_page_content.php');
    ?>
    <div id="beginner_sector">
        <h1 style="margin-top: 2vmax; margin-bottom: 2vmax; background-image: url('img/background.gif'); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-size: 6vmax;">Wielkie rzeczy zaczynają się od małych kroków!</h1>
        <h2>Zapoznaj się z materiałami, które przygotowaliśmy specjalnie dla Ciebie i weź udział w wydarzeniu.</h2>
        <br />
        <a href="zadania.php" class="simple_href" style="font-size: 2vmax;">Archiwum zadań i treści ⟶</a><a href="content.php?id=1" class="simple_href" style="font-size: 2vmax; display: block;">Przewodnik dla początkujących ⟶</a>
        <br />
        <br />
    </div>
    <br style="clear: both;" />
    <?php include(__DIR__.'/../include/portal/main_footer.php'); ?>
    <script>
        var i = 0;
        var txt = '<?php if(isset($general_motd) and strlen($general_motd)>1) { echo($general_motd); } else { echo("You can change MOTD and title in the settings section."); } ?>';
        var speed = 50;

        function typeWriter() {
            if (i < txt.length) {
                document.getElementById("welcome_motd").innerHTML += txt.charAt(i);
                i++;
                setTimeout(typeWriter, speed);
            }
        }
        typeWriter();
    </script>
</body>
</html>