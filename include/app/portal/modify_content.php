<?php
    if(!isset($_GET['page']) || !is_logged_in() || !has_a_priority(3)) kick();
    $content = $_POST['editor_send_content'];
    
    if($_GET['page']=="portal_main")
    {
        $file = fopen("../include/elements/main_page_content.php", "w");
        fwrite($file, $content);
        fclose($file);
    } else if($_GET['page']=="portal_faq")
    {
        $file = fopen("../include/elements/faq_content.php", "w");
        fwrite($file, $content);
        fclose($file);
    } else if($_GET['page']=="portal_contact")
    {
        $file = fopen("../include/elements/contact_info.php", "w");
        fwrite($file, $content);
        fclose($file);
    } else {
        kick();
    }

    redirect("index.php?p=portal");
?>