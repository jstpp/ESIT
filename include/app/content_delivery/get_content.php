<?php
    if(!isset($_GET['mode'], $_GET['cid']) || !($_GET['cid']>0) || filter_var($_GET['cid'], FILTER_VALIDATE_INT)===False) 
    {
        http_response_code(404);
        die("Invalid resource call.");
    } else {
        $cid = filter_var($_GET['cid'], FILTER_VALIDATE_INT);
    }
    
    $db_query = $pdo->prepare('SELECT * FROM PROBLEMS WHERE PROBLEM_ID=:problemid');
    $db_query->execute(['problemid' => $cid]);
    $problem_data = $db_query->fetch();

    if(empty($problem_data)) 
    {
        http_response_code(404);
        die("Resource not found.");
    }

    $publish_time = strtotime($problem_data['publish_time'] ?? 'now');

    if(!($publish_time < strtotime("now")
        || is_logged_in() && $_SESSION['AUTH_LEVEL']<=3
        || is_logged_in() && $problem_data['author_id'] == $_SESSION['AUTH_ID']))
    {
        http_response_code(404);
        die("Resource not found.");
    }

    if($_GET['mode']=="pdffile")
    {        
        if(isset($problem_data['type']) && ($problem_data['type']==1 || $problem_data['type']==5))
        {
            header("Content-type: application/pdf");   
            $root_path = __DIR__.'/../../../include/worker/alg/'.$cid."/pdf/";
            if(file_exists($root_path.$cid.".pdf"))
            {
                header("Content-Length: ".filesize($root_path.$cid.".pdf"));
                readfile($root_path.$cid.".pdf");
                die;
            } else {
                http_response_code(404);
                die("Resource not found.");
            }
        } else {
            http_response_code(404);
            die("Resource not found.");
        }
    } else if($_GET['mode']=="ctffile")
    {
        if(isset($problem_data['type']) && $problem_data['type']==2)
        {
            $root_path = __DIR__.'/../../../include/worker/ctf/'.$cid."/";
            if (!is_dir($root_path)) {
                http_response_code(404);
                die("Resource not found.");
            }

            $files = array_diff(scandir($root_path), ['.', '..']);
            if(empty($files))
            {
                http_response_code(404);
                die("Resource not found.");
            }
            $file = array_values($files)[0];

            if (strpos(realpath($root_path.$file)."/", realpath($root_path)."/") !== 0) {
                http_response_code(404);
                die("Resource not found. Try harder:(");
            }

            if(file_exists($root_path.$file))
            {
                header("Content-type: application/octet-stream");
                header("Content-Length: ".filesize($root_path.$file));
                header('Content-Disposition: attachment; filename="'.rawurlencode($file).'"');
                readfile($root_path.$file);
                die;
            } else {
                http_response_code(404);
                die("Resource not found.");
            }
        } else {
            http_response_code(404);
            die("Resource not found.");
        }
    } else {
        http_response_code(404);
        die("Resource not found.");
    }
?>