<?php
    require_once('../app/modules/composer/vendor/autoload.php');
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    if(isset($_POST['recovery_pass_mail']))
    {
        $db_query = $pdo->prepare("SELECT * FROM USERS WHERE mail=:mail");
        $db_query->execute(['mail' => filter_var($_POST['recovery_pass_mail'], FILTER_SANITIZE_EMAIL)]);

        $users_data = $db_query->fetch();

        if(isset($users_data['name']))
        {
            $recovery_token = generate_recovery_token($users_data['mail']);
            $recovery_link = get_misc_value('general_url')."/login/index.php?passrecoverypass&mail=".$users_data['mail']."&token=".$recovery_token;
            $recovery_msg = __("Hello")." ".$users_data['name']."!<br />".__("It's me - your activation link").":
            <br /><a href='".$recovery_link."'>".$recovery_link."</a>";
            
            $mail = new PHPMailer();
                        
            $mail->IsSMTP();
            $mail->CharSet="UTF-8";
            $mail->Host = get_misc_value('plugin_mailing_module_host');
            $mail->Hostname = get_misc_value('plugin_mailing_module_host');
            $mail->SMTPDebug = $mail_smtp_debug;
            $mail->Port = get_misc_value('plugin_mailing_module_port');
            $mail->SMTPSecure = get_misc_value('plugin_mailing_module_protocol');
            $mail->addCustomHeader('X-Mailer', 'PHPMailer');
            $mail->SMTPAuth = $mail_smtp_auth;
            $mail->IsHTML(true);
            $mail->Username = get_misc_value('plugin_mailing_module_username');
            $mail->Password = get_misc_value('plugin_mailing_module_password');
            $mail->setFrom(get_misc_value('plugin_mailing_module_username'), $mail_name);
            $mail->AddAddress(filter_var($_POST['recovery_pass_mail'], FILTER_SANITIZE_EMAIL));
            $mail->Subject = __("Recover your password");
            $mail->Body = $recovery_msg;

            try {
                if(!$mail->Send()) {
                    redirect("index.php?passrecoverymail&response=fail");
                }
            } catch (Exception $e) {
                redirect("index.php?passrecoverymail&response=fail");
            }
        } else {
            redirect("index.php?passrecoverymail&response=fail");
        }
    } else {
        kick();
    }
?>