<?php
    if(is_logged_in() and has_a_priority(3) and isset($_GET['category']))
    {
        $db_query = $pdo->prepare('DELETE FROM MISC WHERE misc_name LIKE :category');
		$db_query->execute(['category' => $_GET['category']."_%"]);
        foreach($_POST as $key => $value)
        {
            if(str_starts_with($key, $_GET['category'].'_'))
            {
                $db_query = $pdo->prepare('INSERT INTO MISC (misc_name, misc_value) VALUES (:name, :value)');
		        $db_query->execute(['name' => $key, 'value' => $value]);
            }
            if(str_starts_with($key, 'i_allowed_address_'))
            {
                if(!isset($allowed_address_array))
                {
                    $allowed_address_array = array();
                    $db_query = $pdo->prepare('DELETE FROM MISC WHERE misc_name="general_workers_allowed_addr"');
                    $db_query->execute();
                }
                array_push($allowed_address_array, $value);
            }
        }

        if(isset($allowed_address_array))
        {
            $db_query = $pdo->prepare('INSERT INTO MISC (misc_name, misc_value) VALUES ("general_workers_allowed_addr", :value)');
            $db_query->execute(['value' => json_encode($allowed_address_array)]);
        }

        redirect("index.php?p=admin");
    } else {
        kick();
    }
?>