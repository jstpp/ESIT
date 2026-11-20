<?php

class PluginManager
{
    private $pdo;
    private $plugins = array();

    public function __construct($db)
    {
        $this->pdo = $db;
    }


    private function sanitize_plugin_name($pluginName): string
    {
        $pluginName = preg_replace('/[^a-zA-Z0-9_-]/', '', $pluginName);
        if (!empty($pluginName)) {
            return $pluginName;
        }
        throw new InvalidArgumentException('Incorrect plugin name!');
    }


    public function validate_plugins(): bool
    {
        try {
            $db_query = $this->pdo->prepare('SELECT misc_name FROM MISC WHERE misc_name LIKE "community_plugin_%"');
            $db_query->execute();
            while ($row = $db_query->fetch()) {
                $row = substr($row['misc_name'], 17);
                $xdb_query = $this->pdo->prepare('SELECT * FROM PLUGINS WHERE plugin_name=:plugin_name');
                $xdb_query->execute(['plugin_name' => $row]);

                if (!isset($xdb_query->fetch()['plugin_name']) && !$this->install($row)) {
                    return false;
                }
            }
            return true;
        } catch (Throwable $t) {
            extended_exception_handler($t);
            return false;
        }
    }


    public function download($repo, $branch): bool
    {
        if (!has_permission('main.admin.plugins.download')) {
            return false;
        }
        if (empty($repo) || empty($branch)) {
            return false;
        }
        if (!preg_match('/^[a-zA-Z0-9_-]+\/[a-zA-Z0-9_-]+$/', $repo)) {
            return false;
        }
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $branch)) {
            return false;
        }

        $repo_url = 'https://github.com/' . $repo . '/archive/refs/heads/' . $branch . '.zip';
        $dest_path = __DIR__ . '/../../plugins/' . explode("/", $repo)[1];

        try {
            if (!is_dir($dest_path)) {
                mkdir($dest_path, 0755, true);
            }

            $repo_content = file_get_contents($repo_url);
            if (!empty($repo_content)) {
                file_put_contents($dest_path . "/" . $branch . ".zip", $repo_content);
            }

            if (extract_zip($dest_path . "/" . $branch . ".zip", __DIR__ . '/../../plugins/')) {
                unlink($dest_path . "/" . $branch . ".zip");
                delete_directory($dest_path);
                rename($dest_path . "-" . $branch, $dest_path);
                return true;
            } else {
                delete_directory($dest_path);
                return false;
            }
        } catch (Throwable $t) {
            extended_exception_handler($t);
            return false;
        }
    }


    public function install($plugin_name): bool
    {
        if (!has_permission('main.admin.plugins.install')) {
            return false;
        }
        if (!isset($plugin_name)) {
            return false;
        }

        try {
            $clean_name = $this->sanitize_plugin_name($plugin_name);
        } catch (Throwable $t) {
            extended_exception_handler($t);
            return false;
        }
        $setup_file = __DIR__ . "/../../plugins/{$clean_name}/setup.php";

        if (!file_exists($setup_file)) {
            return false;
        }

        try {
            if (!isset($this->plugins[$clean_name])) {
                $this->plugins[$clean_name] = include($setup_file);
            }
            if (!($this->plugins[$clean_name] instanceof PluginInterface)) {
                return false;
            }
            return $this->plugins[$clean_name]->install($this->pdo);
        } catch (Throwable $t) {
            extended_exception_handler($t);
            return false;
        }
    }


    public function uninstall($plugin_name): bool
    {
        if (!has_permission('main.admin.plugins.uninstall')) {
            return false;
        }
        if (!isset($plugin_name)) {
            return false;
        }

        try {
            $clean_name = $this->sanitize_plugin_name($plugin_name);
        } catch (Throwable $t) {
            extended_exception_handler($t);
            return false;
        }
        $setup_file = __DIR__ . "/../../plugins/{$clean_name}/setup.php";

        if (!file_exists($setup_file)) {
            return false;
        }

        try {
            if (!isset($this->plugins[$clean_name])) {
                $this->plugins[$clean_name] = include($setup_file);
            }
            if (!($this->plugins[$clean_name] instanceof PluginInterface)) {
                return false;
            }
            return $this->plugins[$clean_name]->uninstall($this->pdo);
        } catch (Throwable $t) {
            extended_exception_handler($t);
            return false;
        }
    }
}

interface PluginInterface
{
    public function install(PDO $pdo): bool;
    public function uninstall(PDO $pdo): bool;
}
