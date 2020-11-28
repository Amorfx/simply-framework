<?php

namespace Simply\Core\Command;

use Symfony\Component\Filesystem\Filesystem;

class SetupWpConfigCommand extends AbstractWordPressCommand {
    static $commandName = 'simply:config:setup-wp-config';
    function execute($args, $assoc_args) {
        $this->showColorMessage('Setup wp-config with dotenv...', '%b');
        $fs = new Filesystem();
        $nameWpConfig = 'wp-config.sample.php';
        $newName = 'wp-config.php';
        $fs->remove(ABSPATH . '/' . $newName);
        $fs->copy(SIMPLY_RESOURCES_DIRECTORY . '/' . $nameWpConfig, ABSPATH . '/' . $newName);
        $this->showColorMessage('Created wp-config.php file', '%b');
    }
}
