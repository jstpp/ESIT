<?php

    require_once('modules/composer/vendor/autoload.php');
    use MincDev\OtpAuth\OtpAuthenticator;

if (!has_permission('main.save_otp')) {
    kick();
}
if (!isset($_POST['otp_secret'], $_POST['otp_code'])) {
    redirect("/app/?p=settings&error");
}

    $otp = new OtpAuthenticator();

if ($otp->validate($_POST['otp_secret'], trim($_POST['otp_code']))) {
    $_SESSION['AUTH_2FA'] = true;
    $db_query = $pdo->prepare('UPDATE USERS SET 2fa_secret=:secret WHERE USER_ID=:uid');
    $db_query->execute(['secret' => $_POST['otp_secret'], 'uid' => $_SESSION['AUTH_ID']]);
    redirect("/app/?p=settings");
} else {
    redirect("/app/?p=settings&error");
}
