<?php
    if(!isset($_POST['username'], $_POST['name'], $_POST['surname'], $_POST['org'], $_POST['mail'], $_POST['pass'], $_POST['pass_repeat'])) kick();
    if(is_logged_in()) kick();

    try {
        if(!is_string($_POST['username']) 
        || !is_string($_POST['pass'])
        || !is_string($_POST['org']) 
        || !is_string($_POST['mail']) 
        || !is_string($_POST['name'])
        || !is_string($_POST['surname'])) redirect((boolval(get_misc_value('plugin_portal')) ? '../rejestracja.php' : '/login/register.php')."?error");
        if (!preg_match('/^[\p{L}\p{N}_-]+$/u', $_POST['username'])) redirect((boolval(get_misc_value('plugin_portal')) ? '../rejestracja.php' : '/login/register.php')."?error");
        if ($_POST['pass']!=$_POST['pass_repeat']) redirect((boolval(get_misc_value('plugin_portal')) ? '../rejestracja.php' : '/login/register.php')."?error");
        if (mb_strlen($_POST['pass'])<8 || mb_strlen($_POST['pass'])>50 || mb_strlen($_POST['username'])<6) redirect((boolval(get_misc_value('plugin_portal')) ? '../rejestracja.php' : '/login/register.php')."?error");
        if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) redirect((boolval(get_misc_value('plugin_portal')) ? '../rejestracja.php' : '/login/register.php')."?error");
        if (is_an_user($_POST['username']) || is_an_user($_POST['mail'])) redirect((boolval(get_misc_value('plugin_portal')) ? '../rejestracja.php' : '/login/register.php')."?error");
        if (empty(trim($_POST['name'])) || empty(trim($_POST['surname'])) || empty(trim($_POST['org']))) redirect((boolval(get_misc_value('plugin_portal')) ? '../rejestracja.php' : '/login/register.php')."?error");
    }
     catch (Throwable $t) {
        extended_exception_handler($t);
        kick();
    }

    $lock_acquired = False;

    try {
        $db_query = $pdo->prepare('SELECT COUNT(*) AS count FROM USERS');
        $db_query->execute();
        $is_first = ($db_query->fetch()['count']==0) ? True : False;

        if($is_first)
        {
            $lock_name = 'esit_first_user_registration';
            $lock_query = $pdo->prepare(
                'SELECT GET_LOCK(:lock_name, 10)'
            );
            $lock_query->execute([
                'lock_name' => $lock_name
            ]);

            $lock_acquired = ((int)$lock_query->fetchColumn() !== 1) ? False : True;

            if (!$lock_acquired) {
                throw new Exception(
                    'Nie udało się uzyskać blokady rejestracji.'
                );
            }
        }

        $pdo->beginTransaction();

        $db_query = $pdo->prepare('SELECT COUNT(*) AS count FROM USERS');
        $db_query->execute();
        $is_first = ($db_query->fetch()['count']==0) ? True : False;

        $user_id = $pdo->query('SELECT UUID()')->fetchColumn();
        $db_query = $pdo->prepare('INSERT INTO USERS (USER_ID, username, password, mail, name, surname, organization) VALUES (:uid, :username, :password, :mail, :name, :surname, :org)');
        $db_query->execute([
            'uid' => $user_id,
            'username' => $_POST['username'], 
            'password' => password_hash($_POST['pass'], PASSWORD_ARGON2ID),
            'mail' => filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL), 
            'name' => trim($_POST['name']), 
            'surname' => trim($_POST['surname']), 
            'org' => trim($_POST['org'])
        ]); 

        $db_query = $pdo->prepare('INSERT INTO AFFILIATION (user_id, role_id) VALUES (:uid, (SELECT DISTINCT ROLE_ID FROM ROLES WHERE role_name=:role_name))');
        $db_query->execute(['uid' => $user_id, 'role_name' => ($is_first) ? 'superadministrator' : 'user']);

        $pdo->commit();

        if ($lock_acquired) {
            $pdo->query("SELECT RELEASE_LOCK('esit_first_user_registration')");
            $lock_acquired = False;
        }

        redirect("../login/?response=registered");
    } catch (Throwable $t) {
        try {
            if ($pdo->inTransaction()) $pdo->rollBack();
            if ($lock_acquired) $pdo->query("SELECT RELEASE_LOCK('esit_first_user_registration')");
        } catch (Throwable $t2)
        {
            extended_exception_handler($t2);
        }
        extended_exception_handler($t);
        redirect((boolval(get_misc_value('plugin_portal')) ? '../rejestracja.php' : '/login/register.php')."?error");
    }
?>