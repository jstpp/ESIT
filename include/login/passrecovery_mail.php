<?php
    include('../../include/app/core.php');
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
            $recovery_token = hash('sha3-256', $users_data['password']);
            $recovery_link = get_misc_value('general_url')."/login/index.php?passrecoverypass&mail=".$users_data['mail']."&token=".$recovery_token;
            $recovery_msg = "Dzień dobry ".$users_data['name']."!<br />To ja - twój link do odzyskiwania hasła:
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
            $mail->Subject = "Odzyskaj swoje hasło";
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