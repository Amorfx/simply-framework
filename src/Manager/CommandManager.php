<?php

namespace SimplyFramework\Manager;

use SimplyFramework\Command\AbstractWordPressCommand;
use SimplyFramework\Contract\ManagerInterface;

class CommandManager implements ManagerInterface {

    /**
     * @var AbstractWordPressCommand[]
     */
    private $commands;

    public function __construct($commands) {
        $this->commands = $commands;
    }

    public function initialize() {
        add_action('cli_init', function() {
            foreach ($this->commands as $aCommand) {
                $aCommand->register();
            }
        });
    }
}
