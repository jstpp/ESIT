<?php
    if(!is_logged_in() || !has_a_priority(3) || !isset($plugin_manager)) kick();

    if(isset($_GET['call'], $_GET['plugin_name']) and $_GET['call']=="plugin_uninstall")
    {
        try {
            $plugin_manager->uninstall($_GET['plugin_name']);
        } catch (Throwable $t) {
            extended_exception_handler($t);
            redirect("index.php?p=admin&error");
        }
        redirect("index.php?p=admin");
    }

    if(isset($_GET['category']))
    {
        try {
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

            if ($plugin_manager->validate_plugins()) redirect("index.php?p=admin");
            redirect("index.php?p=admin&error");
        } catch (Throwable $t) {
            extended_exception_handler($t);
            redirect("index.php?p=admin&error");
        }
    } else {
        kick();
    }
?>