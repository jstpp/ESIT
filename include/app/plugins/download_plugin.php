<?php
    if(isset($_GET['repo']) and isset($_GET['branch']))
    {
        $repoUrl = 'https://github.com/'.$_GET['repo'].'/archive/refs/heads/'.$_GET['branch'].'.zip';
        $destPath = __DIR__.'/../../plugins/'.htmlentities(explode("/",$_GET['repo'])[1])."/";

        mkdir(__DIR__.'/../../plugins/'.htmlentities(explode("/",$_GET['repo'])[1]));

        file_put_contents($destPath.$_GET['branch'].".zip", 
            file_get_contents($repoUrl)
        );

        $zip = new ZipArchive;
        if ($zip->open($destPath.htmlentities($_GET['branch']).".zip") === TRUE) {
            $zip->extractTo(__DIR__.'/../../plugins/');
            $zip->close();
            redirect("index.php?p=admin#plugins");
        } else {
            echo "Błąd przy rozpakowywaniu ZIP.";
        }
    } else {
        kick();
    }
?>