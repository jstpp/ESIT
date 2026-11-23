<?php

    require_once('../app/modules/composer/vendor/autoload.php');
    use MincDev\OtpAuth\OtpAuthenticator;

if (isset($_SESSION['AUTH_ID']) && isset($_POST['auth_otp_code'])) {
    $otp = new OtpAuthenticator();

    if ($otp->validate($_SESSION['AUTH_2FA_SECRET'], trim($_POST['auth_otp_code']))) {
        $_SESSION['AUTH_2FA_VALID'] = true;
        redirect('../app');
    } else {
        redirect('index.php?2fa');
    }
}

if (!isset($_POST['auth_username_or_mail']) || !isset($_POST['auth_password'])) {
    redirect('index.php?response=failed');
}

    $db_query = $pdo->prepare('SELECT * FROM USERS WHERE username=:usr1 OR mail=:usr2');
    $db_query->execute(['usr1' => $_POST['auth_username_or_mail'], 'usr2' => $_POST['auth_username_or_mail']]);

if ($row = $db_query->fetch()) {
    if (password_verify($_POST['auth_password'], $row['password'])) {
        session_regenerate_id(true);
        $_SESSION['AUTH_ID'] = $row['USER_ID'];
        $_SESSION['AUTH_USERNAME'] = $row['username'];
        $_SESSION['AUTH_NAME'] = $row['name'];
        $_SESSION['AUTH_SURNAME'] = $row['surname'];
        $_SESSION['AUTH_MAIL'] = $row['mail'];
        $_SESSION['AUTH_LAST_LOGIN'] = $row['lastlogin'];
        $_SESSION['AUTH_ROLE'] = get_roles($row['USER_ID'])[0];
        $_SESSION['AUTH_2FA'] = ($row['2fa_secret'] != '-') ? true : false;
        $_SESSION['AUTH_2FA_SECRET'] = $row['2fa_secret'];
        $_SESSION['AUTH_2FA_VALID'] = !$_SESSION['AUTH_2FA'];
        $_SESSION['SESSION_TIMEOUT'] = time() + 60 * 60 * 5; # 5h
        regenerate_csrf_token();

        $db_query = $pdo->prepare('UPDATE USERS SET lastlogin=:lastlogin WHERE USER_ID=:uid');
        $db_query->execute(['lastlogin' => date('Y/m/d H:i:s'), 'uid' => $_SESSION['AUTH_ID']]);

        if ($_SESSION['AUTH_2FA_VALID']) {
            redirect('../app');
        } else {
            redirect('index.php?2fa');
        }
    } else {
        redirect('index.php?response=failed');
    }
} else {
    redirect('index.php?response=failed');
}
