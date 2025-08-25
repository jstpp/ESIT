<?php
    include('../../include/config/config_init.php');
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
            $recovery_link = $org_link."/login/index.php?passrecoverypass&mail=".$users_data['mail']."&token=".$recovery_token;
            $recovery_msg = "Dzień dobry ".$users_data['name']."!<br />To ja - twój link do odzyskiwania hasła:
            <br /><a href='".$recovery_link."'>".$recovery_link."</a>";
            
            $mail = new PHPMailer();
                        
            $mail->IsSMTP();
            $mail->CharSet="UTF-8";
            $mail->Host = $mail_host;
            $mail->Hostname = $mail_host;
            $mail->SMTPDebug = $mail_smtp_debug;
            $mail->Port = $mail_port;
            $mail->SMTPSecure = $mail_smtp_secure;
            $mail->addCustomHeader('X-Mailer', 'PHPMailer');
            $mail->SMTPAuth = $mail_smtp_auth;
            $mail->IsHTML(true);
            $mail->Username = $mail_username;
            $mail->Password = $mail_password;
            $mail->setFrom($mail_username, $mail_name);
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