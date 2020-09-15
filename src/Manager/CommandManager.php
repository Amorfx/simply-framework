<?php

namespace SimplyFramework\Manager;

use SimplyFramework\Command\ClearCacheCommand;
use SimplyFramework\Contract\ManagerInterface;

class CommandManager implements ManagerInterface {
    public function initialize() {
        add_action('cli_init', function() {
           new ClearCacheCommand();
        });
    }
}
