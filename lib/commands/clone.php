<?php

function pf_clone($argv) {
    $phpfog = new PHPFog();

    $raw_app_id = array_shift($argv);
    $directory = array_shift($argv);

    if ($raw_app_id=="") { return false; }

    $app_id = intval($raw_app_id);

    if ("$app_id" != $raw_app_id) {
        $app_id = $phpfog->get_app_id_by_name($raw_app_id);
        if ($app_id == null) {
            failure_message("No app found with the name: $raw_app_id".PHP_EOL);
            return true;
        }
    }

    try {
        $app = $phpfog->get_app($app_id);
    } catch (PestJSON_NotFound $e) {
        failure_message($phpfog->get_api_error_message().PHP_EOL);
        return true;
    }

    $git_url = $app['git_url'];
    $ssh_identifier = preg_replace("/[^A-Za-z0-9-]/", "-", $phpfog->username());

    preg_match("/.*?:(.*?)$/", $git_url, $matches);

    $git_url = $ssh_identifier.":".$matches[1];
    echo "git clone $git_url".PHP_EOL;
    
    if (execute("git clone $git_url $directory") > 0) {
        echo failure_message("Failed to clone app. Run 'pf setup' to insure you have your ssk key installed correctly.");
    }

    return true;
}