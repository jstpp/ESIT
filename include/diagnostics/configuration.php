<?php

function configuration_diagnostics(): array
{
    $results = array();

    ### Configuration variables tips

    # Check for default general_url values
    if (
        get_misc_value('general_url') == "localhost"
        || get_misc_value('general_url') == "http://localhost"
    ) {
        array_push($results, [
            "category" => "warning",
            "content" => "Variable <code>general_url</code> is set to <code>" . htmlentities(get_misc_value('general_url')) . "</code>. It may result in multiple problems with api communication."
        ]);
    }

    # Check for debugging options online
    if (boolval(get_misc_value('plugin_debugging'))) {
        array_push($results, [
            "category" => "info",
            "content" => "Advanced debugging options are active."
        ]);
    }

    # Check if gettext present
    if (!function_exists('gettext')) {
        array_push($results, [
            "category" => "warning",
            "content" => "Gettext is not active. Translations will not work."
        ]);
    }

    # Check if https is on
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
        array_push($results, [
            "category" => "warning",
            "content" => "It is strongly recommended to use https instead of http."
        ]);
    }

    # Check if https is on & Advanced Header Security Module is on
    if (!filter_var(get_misc_value('general_advanced_header_security'), FILTER_VALIDATE_BOOLEAN)) {
        array_push($results, [
            "category" => "warning",
            "content" => "It is strongly recommended to use Advanced Header Security Module (available with https connections)."
        ]);
    }

    foreach (scandir(__DIR__ . "/tests") as $path) {
        if (in_array($path, [".", ".."])) {
            continue;
        }
        $path = __DIR__ . "/tests/" . $path;
        include_once($path);
    }

    return (has_permission('main.display.admin.diagnostics')) ? $results : [];
}
