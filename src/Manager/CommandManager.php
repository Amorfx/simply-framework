<?php

namespace Simply\Core\Manager;

use Simply\Core\Command\AbstractWordPressCommand;
use Simply\Core\Contract\ManagerInterface;

class CommandManager implements ManagerInterface
{
    /**
     * @var AbstractWordPressCommand[]
     */
    private $commands;

    public function __construct($commands)
    {
        $this->commands = $commands;
    }

    public function initialize()
    {
        add_action('cli_init', array($this, 'registerCommands'));
    }

    public function registerCommands()
    {
        foreach ($this->commands as $aCommand) {
            $aCommand->register();
        }
    }
}
