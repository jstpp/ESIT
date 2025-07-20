<?php
    if(isset($_POST['username']) 
    and isset($_POST['name'])
    and isset($_POST['surname'])
    and isset($_POST['org'])
    and isset($_POST['mail'])
    and isset($_POST['pass'])
    and isset($_POST['pass_repeat'])
    and !is_logged_in())
    {
        $db_query = $pdo->prepare('SELECT COUNT(*) AS count FROM USERS');
        $db_query->execute();

        if($db_query->fetch()['count']==0)
        {
            $priority = 1;
        } else {
            $priority = 10;
        }

        if(!is_an_user($_POST['username'])
            and !is_an_user($_POST['mail'])
            and preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['username'])==$_POST['username'])
        {

            if(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) header("Location: ../rejestracja.php?error");

            $db_query = $pdo->prepare('INSERT INTO USERS (username, password, role, mail, name, surname, organization) VALUES (:username, :password, :priority, :mail, :name, :surname, :org)');
            $db_query->execute([
                'username' => htmlentities($_POST['username']), 
                'password' => hash('sha3-384', $_POST['pass']),
                'priority'=> $priority, 
                'mail' => $_POST['mail'], 
                'name' => $_POST['name'], 
                'surname' => $_POST['surname'], 
                'org' => $_POST['org']
            ]);
            header("Location: ../login/?response=registered");
        } else {
            header("Location: ../rejestracja.php?error");
        }
    } else {
        kick();
    }
?>