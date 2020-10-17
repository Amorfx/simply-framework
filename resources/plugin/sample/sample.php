<?php
/*
Plugin Name: --PLUGIN_NAME--
Description: --PLUGIN_DESCRIPTION--
Author: --PLUGIN_AUTHOR--
Author URI: --PLUGIN_AUTHOR_URI--
Version: --PLUGIN_VERSION--
*/

add_filter('simply_config_directories', function($arrayDirectories) {
    $arrayDirectories[] = __DIR__ . '/config';
    return $arrayDirectories;
});
