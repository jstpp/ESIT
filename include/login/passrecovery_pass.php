<?php
    include('../../include/app/core.php');

    if(isset($_SESSION['pr_mail']) and isset($_SESSION['pr_token']) and isset($_POST['recovery_pass_1']) and isset($_POST['recovery_pass_2']))
    {
        $db_query = $pdo->prepare("SELECT * FROM USERS WHERE mail=:mail");
        $db_query->execute(['mail' => filter_var($_SESSION['pr_mail'], FILTER_SANITIZE_EMAIL)]);
        $users_data = $db_query->fetch();
        if($_SESSION['pr_token']==hash('sha3-256', $users_data['password']))
        {
            if($_POST['recovery_pass_1']==$_POST['recovery_pass_2'])
            {
                $db_query = $pdo->prepare("UPDATE USERS SET password=:newpass WHERE USER_ID=:id");
                $db_query->execute(['newpass' => hash('sha3-384', $_POST['recovery_pass_1']), 'id' => $users_data['USER_ID']]);
                redirect("index.php?response=passrecoverysuccess");
            } else {
                redirect("index.php?passrecoverypass&response=fail");
            }
        } else {
            redirect("index.php?passrecoverypass&response=fail");
        }
    } else {
        redirect("index.php?passrecoverypass&response=fail");
    }
?>