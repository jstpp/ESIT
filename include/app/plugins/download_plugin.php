<?php
    if(!isset($plugin_manager, $_GET['repo'], $_GET['branch']) || !has_permission('main.admin.plugins.download')) kick();
    if($plugin_manager->download($_GET['repo'], $_GET['branch']))
    {
        redirect("index.php?p=admin#plugins");
    } else {
        redirect("index.php?p=admin#plugins&error");
    }
?>