<?php
    require_once('modules/composer/vendor/autoload.php');
    $purifier_config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($purifier_config);

    if(!isset($_GET['page']) || !is_logged_in() || !has_permission('main.portal.modify_content')) kick();
    $content = $purifier->purify($_POST['editor_send_content']);
    
    try {
        if($_GET['page']=="portal_main")
        {
            $file = fopen("../include/elements/main_page_content.html", "w");
            if (!$file) throw new Exception("modify_content.php: fopen of main_page_content.html failed");
            if (!fwrite($file, $content)) throw new Exception("modify_content.php: fwrite failed.");
            if (!fclose($file)) throw new Exception("modify_content.php: fclose failed.");
        } else if($_GET['page']=="portal_faq")
        {
            $file = fopen("../include/elements/faq_content.html", "w");
            if (!$file) throw new Exception("modify_content.php: fopen of faq_content.html failed");
            if (!fwrite($file, $content)) throw new Exception("modify_content.php: fwrite failed.");
            if (!fclose($file)) throw new Exception("modify_content.php: fclose failed.");
        } else if($_GET['page']=="portal_contact")
        {
            $file = fopen("../include/elements/contact_info.html", "w");
            if (!$file) throw new Exception("modify_content.php: fopen of contact_info.html failed");
            if (!fwrite($file, $content)) throw new Exception("modify_content.php: fwrite failed.");
            if (!fclose($file)) throw new Exception("modify_content.php: fclose failed.");
        } else {
            kick();
        }
    } catch (Throwable $t) {
		extended_exception_handler($t);
	}

    redirect("index.php?p=portal");
?>