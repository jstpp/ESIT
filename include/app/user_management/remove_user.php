<?php
    if(isset($_GET['uid']))
    {
        $db_query = $pdo->prepare('DELETE FROM USERS WHERE USER_ID=:uid AND role>:priority');
        $db_query->execute([
            'uid' => filter_var($_GET['uid'], FILTER_VALIDATE_INT),
            'priority' => $_SESSION['AUTH_LEVEL']
        ]);

        header("Location: index.php?p=admin");
    } else {
        kick();
    }
?>