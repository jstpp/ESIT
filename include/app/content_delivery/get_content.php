<?php
    if(isset($_GET['mode']) and isset($_GET['cid']))
    {
        $db_query = $pdo->prepare('SELECT * FROM PROBLEMS WHERE PROBLEM_ID=:problemid');
        $db_query->execute(['problemid' => filter_var($_GET['cid'], FILTER_VALIDATE_INT)]);
        $problem_data = $db_query->fetch();

        if($_GET['mode']=="pdffile")
        {        
            if((isset($problem_data['type'])
                and $problem_data['type']==1 
                or $problem_data['type']==5) #if request has a proper type (ALG or FORM)
                and (strtotime($problem_data['publish_time']) < strtotime("now") #if it asks for allowed resource
                or $_SESSION['AUTH_LEVEL']<=3 #or if it is a request from administrator
                or $problem_data['author_id'] == $_SESSION['AUTH_ID'])) #or if it is a request from content owner/author
            {
                header("Content-type: application/pdf");   
                $root_path = __DIR__.'/../../../include/worker/alg/'.filter_var($_GET['cid'], FILTER_VALIDATE_INT)."/pdf/";
                if(file_exists($root_path.filter_var($_GET['cid'], FILTER_VALIDATE_INT).".pdf"))
                {
                    echo(file_get_contents($root_path.filter_var($_GET['cid'], FILTER_VALIDATE_INT).".pdf"));
                } else {
                    echo("Resource doesn't exist.");
                }
            } else {
                echo("Resource unavailable.");
            }
        } else if($_GET['mode']=="ctffile")
        {
            if(isset($problem_data['type'])
                and $problem_data['type']==2 #if request has a proper type (CTF)
                and (strtotime($problem_data['publish_time']) < strtotime("now")
                or $_SESSION['AUTH_LEVEL']<=3
                or $problem_data['author_id'] == $_SESSION['AUTH_ID']))
            {
                $root_path = __DIR__.'/../../../include/worker/ctf/'.filter_var($_GET['cid'], FILTER_VALIDATE_INT)."/";
                if(file_exists($root_path.scandir($root_path, SCANDIR_SORT_DESCENDING)[0]))
                {
                    header("Content-type: application/octet-stream");
                    header('Content-Disposition: attachment; filename="'.scandir($root_path, SCANDIR_SORT_DESCENDING)[0].'"');
                    echo(file_get_contents($root_path.scandir($root_path, SCANDIR_SORT_DESCENDING)[0]));
                } else {
                    echo("Resource in doesn't exist.");
                }
            } else {
                echo("Resource unavailable.");
            }
        }
    } else {
        echo("Invalid resource call.");
    }
?>