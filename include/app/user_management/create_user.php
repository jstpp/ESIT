<?php
    if(isset($_POST['username']) 
    and isset($_POST['name'])
    and isset($_POST['surname'])
    and isset($_POST['org'])
    and isset($_POST['mail'])
    and isset($_POST['priority'])
    and isset($_POST['password'])
    and has_a_priority(filter_var($_POST['priority'], FILTER_VALIDATE_INT)-1))
    {
        if(!is_an_user($_POST['username'])
            and !is_an_user($_POST['mail']))
        {
            $db_query = $pdo->prepare('INSERT INTO USERS (username, password, role, mail, name, surname, organization) VALUES (:username, :password, :priority, :mail, :name, :surname, :org)');
            $db_query->execute([
                'username' => htmlentities($_POST['username']), 
                'password' => password_hash($_POST['password'], PASSWORD_ARGON2ID),
                'priority'=>filter_var($_POST['priority'], FILTER_VALIDATE_INT), 
                'mail' => $_POST['mail'], 
                'name' => $_POST['name'], 
                'surname' => $_POST['surname'], 
                'org' => $_POST['org']
            ]);
            redirect("index.php?p=admin");
        } else {
            redirect("index.php?p=admin&error");
        }
    } else {
        kick();
    }
?>