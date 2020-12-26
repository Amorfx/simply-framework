<?php

add_filter('simply_config_directories', function($arrayDirectories) {
    $arrayDirectories[] = __DIR__ . '/config';
    return $arrayDirectories;
});
