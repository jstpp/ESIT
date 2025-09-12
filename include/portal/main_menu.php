<?php
	$db_query = $pdo->prepare('SELECT * FROM MISC WHERE misc_name LIKE "social_media_%"');
	$db_query->execute();
	while($row = $db_query->fetch())
	{
		if($row['misc_name']=="social_media_yt") $socialmedia_yt = $row;
		if($row['misc_name']=="social_media_ig") $socialmedia_ig = $row;
		if($row['misc_name']=="social_media_fb") $socialmedia_fb = $row;
	}
?>

<div id="main_menu_sector">
    <?php
        if(isset($socialmedia_fb) or isset($socialmedia_ig) or isset($socialmedia_yt))
        {
            echo('<span style="margin-left: 1vmax;"></span>');
        }
        if(isset($socialmedia_fb) and strlen($socialmedia_fb['misc_value'])>1)
        {
            echo('<a aria_label="Facebook" href="'.$socialmedia_fb['misc_value'].'"><i class="media_buttons fa fa-facebook"></i></a>');
        }
        if(isset($socialmedia_ig) and strlen($socialmedia_ig['misc_value'])>1)
        {
            echo('<a aria_label="Instagram" href="'.$socialmedia_ig['misc_value'].'"><i class="media_buttons fa fa-instagram"></i></a>');
        }
        if(isset($socialmedia_yt) and strlen($socialmedia_yt['misc_value'])>1)
        {
            echo('<a aria_label="YouTube" href="'.$socialmedia_yt['misc_value'].'"><i class="media_buttons fa fa-youtube"></i></a>');
        }
        
    ?>
    <?php 
        if(is_logged_in())
        {
            echo('<a class="transparent_btt" style="margin-right: 1vmax;" href="app">Wróć do aplikacji</a>');
        } else {
            echo('<a class="transparent_btt" style="margin-right: 1vmax;" href="login">Logowanie</a>
    <a class="transparent_btt" href="rejestracja.php">Rejestracja</a>');
        }
    ?>
</div>  
<div id="main_menu_sector_bottom">
    <a href="index.php">STRONA GŁÓWNA</a>
    <a href="aktualnosci.php">AKTUALNOŚCI</a>
    <a href="zadania.php">ZADANIA</a>
    <a href="dokumenty.php">DOKUMENTY</a>
    <a href="kontakt.php">KONTAKT</a>
    <a href="faq.php">FAQ</a>
</div> 
<script>
    window.onscroll = function() {scrFunction()};
    function scrFunction() {
        if (window.pageYOffset > document.getElementById("main_menu_sector").offsetTop) {
            document.getElementById("main_menu_sector").classList.add("menu_dark_top");
            document.getElementById("main_menu_sector_bottom").classList.add("menu_dark_bottom");
        } else {
            document.getElementById("main_menu_sector").classList.remove("menu_dark_top");
            document.getElementById("main_menu_sector_bottom").classList.remove("menu_dark_bottom");
        }
    } 
</script>