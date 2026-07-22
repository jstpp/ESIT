<?php
    function configuration_diagnostics() {
        $results = array();

        ### Configuration variables tips
        if(get_misc_value('general_url')=="localhost" || get_misc_value('general_url')=="http://localhost") {
            array_push($results, [
                "category" => "warning",
                "content" => "Variable <code>general_url</code> is set to <code>".htmlentities(get_misc_value('general_url'))."</code>. It may result in multiple problems with api communication."
            ]);
        }
        if(boolval(get_misc_value('plugin_debugging'))) {
            array_push($results, [
                "category" => "info",
                "content" => "Advanced debugging options are active."
            ]);
        }
        return $results;
    }
?>