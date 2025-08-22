<?php
    session_start();

    $org_link = "localhost"; #Link to website, where this app is hosted by You.
    $site_domains = array($org_link, "localhost"); #Used for worker communication and file upload. Keep in mind that first URL in array will be used by worker to return results
    $site_timezone = "Europe/Warsaw";
    $error_link =  $org_link."/error"; #Your custom error site. Feel free to change it...
    $display_debug = false; #Set to true if You would like to see more debug messages

    $db_host = "mysql"; #MySQL host
    $db_username = "esit_db"; #MySQL username
    $db_password = "esit_db"; #MySQL password
    $db_database = "esit_db"; #MySQL db name
    $db_charset = "utf8"; #MySQL charset

    $rabbit_mq_host = "rabbitmq"; #Broker's host
    $rabbit_mq_port = 5672; #Broker's access port
    $rabbit_mq_user = "esit_user"; #Broker's access username
    $rabbit_mq_password = "123456"; #Broker's access password

    $worker_network_private_key = "write_something_complicated_here!"; #Change it to provide higher level of data safety during transfer via API
    $worker_trusted_ips = array($org_link, "localhost", "worker"); #Define, which IPs can be a part of network.




    date_default_timezone_set($site_timezone);
    
    $dsn = "mysql:host=$db_host;dbname=$db_database;options='--client_encoding=$db_charset'";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $db_username, $db_password, $options);

    if($display_debug==true)
    {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
    }

?>