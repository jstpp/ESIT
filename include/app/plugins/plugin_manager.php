<?php
    class PluginManager {
        private $pdo;
        private  $plugins = array();

        public function __construct($db) {
            $this->pdo = $db;
        }


        private function sanitize_plugin_name($pluginName): string
        {
            $pluginName = preg_replace('/[^a-zA-Z0-9_-]/', '', $pluginName);
            if(!empty($pluginName)) return $pluginName;
            $t = new InvalidArgumentException('Incorrect plugin name!');
            extended_exception_handler($t);
            throw $t;
        }


        public function validate_plugins(): bool
        {
            try {
                $db_query = $this->pdo->prepare('SELECT misc_name FROM MISC WHERE misc_name LIKE "community_plugin_%"');
                $db_query->execute();
                while($row = $db_query->fetch())
                {
                    $row = substr($row['misc_name'], 17);
                    $xdb_query = $this->pdo->prepare('SELECT * FROM PLUGINS WHERE plugin_name=:plugin_name');
                    $xdb_query->execute(['plugin_name' => $row]);

                    if(!isset($xdb_query->fetch()['plugin_name']) && !$this->install($row)) return False;
                }
                return True;
            } catch (Throwable $t) {
                extended_exception_handler($t);
                return False;
            }
        }


        public function download($repo, $branch): bool
        {
            if (empty($repo) || empty($branch)) return False;
            if (!preg_match('/^[a-zA-Z0-9_-]+\/[a-zA-Z0-9_-]+$/', $repo)) return False;
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $branch)) return False;

            $repoUrl = 'https://github.com/'.$repo.'/archive/refs/heads/'.$branch.'.zip';
            $destPath = __DIR__.'/../../plugins/'.explode("/",$repo)[1];

            try {
                if(!is_dir($destPath)) mkdir($destPath);

                $repoContent = file_get_contents($repoUrl);
                if(!empty($repoContent)) file_put_contents($destPath."/".$branch.".zip", $repoContent);

                $zip = new ZipArchive;
                if ($zip->open($destPath."/".$branch.".zip") === TRUE) {
                    $zip->extractTo(__DIR__.'/../../plugins/');
                    $zip->close();
                    unlink($destPath."/".$branch.".zip");
                    delete_directory($destPath);
                    rename($destPath."-".$branch, $destPath);
                    return True;
                } else {
                    return False;
                }
            } catch (Throwable $t) {
                extended_exception_handler($t);
                return False;
            }
        }


        public function install($pluginName): bool
        {
            if(!isset($pluginName)) return False;

            $cleanName = $this->sanitize_plugin_name($pluginName);
            $setupFile = __DIR__."/../../plugins/{$cleanName}/setup.php";

            if (!file_exists($setupFile)) return false;

            try {
                if(!isset($plugins[$cleanName])) $plugins[$cleanName] = include($setupFile);
                if (!($plugins[$cleanName] instanceof PluginInterface)) return False;
                return $plugins[$cleanName]->install($this->pdo);
            } catch(Throwable $t) {
                extended_exception_handler($t);
                return False;
            }
        }


        public function uninstall($pluginName): bool
        {
            if(!isset($pluginName)) return False;

            $cleanName = $this->sanitize_plugin_name($pluginName);
            $setupFile = __DIR__."/../../plugins/{$cleanName}/setup.php";

            if (!file_exists($setupFile)) return false;

            try {
                if(!isset($plugins[$cleanName])) $plugins[$cleanName] = include($setupFile);
                if (!($plugins[$cleanName] instanceof PluginInterface)) return False;
                return $plugins[$cleanName]->uninstall($this->pdo);
            } catch(Throwable $t) {
                extended_exception_handler($t);
                return False;
            }
        }

    }

    interface PluginInterface {
        public function install(PDO $pdo): bool;
        public function uninstall(PDO $pdo): bool;
    }
?>