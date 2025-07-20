<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    http_response_code(200);

    function zip_inout($problem_id)
    {
        $pathdir = __DIR__.'/../alg/'.$problem_id.'/'; 
        $zipcreated = __DIR__.'/../alg/'.$problem_id."/".$problem_id.".zip";
        $zip = new ZipArchive;

        if($zip -> open($zipcreated, ZipArchive::CREATE ) === TRUE) 
        {
            $files = scandir($pathdir);
            foreach ($files as $file) {
                if ($file == '.' || $file == '..') continue;
                $zip -> addEmptyDir($file);
                if(is_dir($pathdir.$file))
                {
                    $dir = opendir($pathdir.$file);
                    while($next_file = readdir($dir)) {
                        if ($next_file == '.' || $next_file == '..') continue;
                        if(is_file($pathdir.$file."/".$next_file)) {
                            $zip -> addFile($pathdir.$file."/".$next_file, $file."/".$next_file);
                        }
                    }
                }
            }
            $zip ->close();
        }
        return $zipcreated;
    }

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    $tag = base64_decode($data['tag']);

    $content = json_decode(openssl_decrypt($data['content'], 'AES-128-GCM', $worker_network_private_key, $options=0, base64_decode($data['nonce']), $tag), true);

    if(isset($content['worker_addr']))
    {
        if(is_dir(__DIR__.'/../alg/'.$content['problem_id']."/"))
        {
            echo(base64_encode(file_get_contents(zip_inout($content['problem_id']))));
        } else {
            echo('Problem ('.$content["problem_id"].') inout not found. '.__DIR__.'/../alg/'.$content['problem_id']." doesn't exist");
            die;
        }
    } else {
        header("HTTP/1.0 404 Not Found");
        die;
    }
?>