<?php
    if(isset($_POST['username']) 
    and isset($_POST['name'])
    and isset($_POST['surname'])
    and isset($_POST['org'])
    and isset($_POST['mail'])
    and isset($_POST['priority'])
    and isset($_GET['uid'])
    and has_a_priority(filter_var($_POST['priority'], FILTER_VALIDATE_INT)-1))
    {
        $db_query = $pdo->prepare('UPDATE USERS SET username=:username, role=:priority, mail=:mail, name=:name, surname=:surname, organization=:org WHERE USER_ID=:uid');
        $db_query->execute([
            'username' => htmlentities($_POST['username']), 
            'priority'=>filter_var($_POST['priority'], FILTER_VALIDATE_INT), 
            'mail' => $_POST['mail'], 
            'name' => $_POST['name'], 
            'surname' => $_POST['surname'], 
            'org' => $_POST['org'], 
            'uid' => filter_var($_GET['uid'], FILTER_VALIDATE_INT)
        ]);

        header("Location: index.php?p=admin");
    } else {
        kick();
    }
?>