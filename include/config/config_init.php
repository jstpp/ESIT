<?php
    #session_start();

    ### GENERAL CONFIG ###
    #$org_link = "localhost"; #Link to website, where this app is hosted by You.
    #$site_domains = array(get_misc_value('general_url'), "localhost"); #Used for worker communication and file upload. Keep in mind that first URL in array will be used by worker to return results
    #$site_timezone = "Europe/Warsaw"; #Your timezone
    #$error_link =  $org_link."/error"; #Your custom error site. Feel free to change it...
    #$display_debug = false; #Set to true if You would like to see more debug messages

    #### DATABASE CONFIG ###
    #$db_host = "mysql"; #MySQL host
    #$db_username = "esit_db"; #MySQL username
    #$db_password = "esit_db"; #MySQL password
    #$db_database = "esit_db"; #MySQL db name
    #$db_charset = "utf8"; #MySQL charset

    #### BROKER CONFIG ###
    #$rabbit_mq_host = "rabbitmq"; #Broker's host
    #$rabbit_mq_port = 5672; #Broker's access port
    #$rabbit_mq_user = "esit_user"; #Broker's access username
    #$rabbit_mq_password = "123456"; #Broker's access password

    ### WORKER CONFIG ###
    #$worker_network_private_key = "write_something_complicated_here!"; #Change it to provide higher level of data safety during transfer via API
    #$worker_trusted_ips = array($org_link, "localhost", "worker"); #Define, which IPs can be a part of network.

    ### MAILING CONFIG ###
    #$enable_mailing_module = false;
    #$mail_host = "example.com"; #Mailing server
    #$mail_port = 587; #Port
    #$mail_username = "example@example.com"; #Username in mailing server
    #$mail_password = "Example123!"; #Password in mailing server
    #$mail_name = "ESIT Mailing Module"; #Name of the sender
    #$mail_smtp_debug = 0; #SMTP debugging
    #$mail_smtp_secure = 'starttls'; #Enable TLS encryption
    #$mail_smtp_auth = true; #Enable SMTP auth

    #date_default_timezone_set($site_timezone);
    
    #$dsn = "mysql:host=$db_host;dbname=$db_database;options='--client_encoding=$db_charset'";
    #$options = [
    #    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    #    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    #    PDO::ATTR_EMULATE_PREPARES   => false,
    #];
    #$pdo = new PDO($dsn, $db_username, $db_password, $options);

    #if($display_debug==true)
    #{
    #    ini_set('display_errors', '1');
    #    ini_set('display_startup_errors', '1');
    #    error_reporting(E_ALL);
    #}

?>