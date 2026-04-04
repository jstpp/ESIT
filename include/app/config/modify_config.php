<?php
    function plugin_install($plugin_name)
    {
        if(!isset($plugin_name))
        {
            return False;
        } else {
            try {
                include(__DIR__."/../../plugins/".$plugin_name."/setup.php");
                install();
                return True;
            } catch(Exception $e) {
                echo($e);
                return False;
            }
        }
    }

    function validate_plugins()
    {
        global $pdo;
        $db_query = $pdo->prepare('SELECT misc_name FROM MISC WHERE misc_name LIKE "community_plugin_%"');
		$db_query->execute();
        while($row = $db_query->fetch())
		{
            $row = substr($row['misc_name'], 17);
            $xdb_query = $pdo->prepare('SELECT * FROM PLUGINS WHERE plugin_name=:plugin_name');
		    $xdb_query->execute(['plugin_name' => $row]);

            if(!isset($xdb_query->fetch()['plugin_name'])) {
                if (!plugin_install($row)) return False;
            }
        }
        return True;
    }

    function deleteDirectory($dirPath) {
        if (!is_dir($dirPath)) {
            return false;
        }

        $files = array_diff(scandir($dirPath), array('.', '..'));

        foreach ($files as $file) {
            $filePath = $dirPath . DIRECTORY_SEPARATOR . $file;
            if (is_dir($filePath)) {
                deleteDirectory($filePath);
            } else {
                unlink($filePath);
            }
        }

        return rmdir($dirPath);
    }

    if(is_logged_in() and has_a_priority(3) and isset($_GET['call']) and $_GET['call']=="plugin_uninstall" and isset($_GET['plugin_name']))
    {
        try {
            if(file_exists(__DIR__."/../../plugins/".$_GET['plugin_name']."/setup.php"))
            {
                include(__DIR__."/../../plugins/".$_GET['plugin_name']."/setup.php");
                uninstall();
                deleteDirectory(__DIR__."/../../plugins/".$_GET['plugin_name']);
            } else {
                deleteDirectory(__DIR__."/../../plugins/".$_GET['plugin_name']);
            }
        } catch (Exception $e) {
            echo("Uninstalling unsuccessful.");
        }
        redirect("index.php?p=admin");
    }
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

        if (validate_plugins()) redirect("index.php?p=admin");
    } else {
        kick();
    }
?>