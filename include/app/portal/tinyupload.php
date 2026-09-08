<?php
    if(!has_a_priority(3)) kick();

    try {
        $accepted_origins = array("http://localhost", "http://192.168.1.1", get_misc_value('general_url'));
        $imageFolder = "../img/articles/content/";
        
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
                header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
            } else {
                header("HTTP/1.1 403 Origin Denied");
                exit;
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header("Access-Control-Allow-Methods: POST, OPTIONS");
            exit;
        }
        
        if (empty($_FILES)) {
            header("HTTP/1.1 400 No file uploaded");
            exit;
        }

        reset ($_FILES);
        $temp = current($_FILES);

        if (!isset($temp['error']) || $temp['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($temp['tmp_name'])) {
            header("HTTP/1.1 500 Server Error");
            exit;
        }

        if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
            header("HTTP/1.1 400 Invalid file name.");
            exit;
        }
        
        $extension = strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, array("gif", "jpg", "png", "webp", "jpeg"))) {
            header("HTTP/1.1 400 Invalid extension.");
            exit;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($temp['tmp_name']);

        $allowed_mimes = array(
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        );

        if (!in_array($mime_type, $allowed_mimes, true)) {
            header("HTTP/1.1 400 Invalid image MIME type.");
            exit;
        }
        
        $filetowrite = $imageFolder.date('Ymd_His').'_'.bin2hex(random_bytes(16)).'.'.$extension;
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? "https://" : "http://";
        $baseurl = $protocol.$_SERVER["HTTP_HOST"].rtrim(dirname($_SERVER['REQUEST_URI']), "/")."/";
        
        if (move_uploaded_file($temp['tmp_name'], $filetowrite)) {
            header('Content-Type: application/json');
            echo json_encode(array('location' => $baseurl.$filetowrite)); 
        } else {
            header("HTTP/1.1 500 Server Error - Failed to save file.");
            exit;
        }
    } catch (Throwable $t)
    {
        extended_exception_handler($t);
        header("HTTP/1.1 500 Server Error");
        exit;
    }
?>