<?php
    if(is_an_user($_GET['value']))
    {
        echo("EXISTS");
    } else {
        echo("OK");
    }
?>