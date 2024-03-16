<?php

namespace Simply\Core\Manager;

use Simply\Core\Command\AbstractWordPressCommand;
use Simply\Core\Contract\ManagerInterface;

class CommandManager implements ManagerInterface
{
    /**
     * @var AbstractWordPressCommand[]
     */
    private iterable $commands;

    /**
     * @param AbstractWordPressCommand[] $commands
     */
    public function __construct(iterable $commands)
    {
        $this->commands = $commands;
    }

    public function initialize(): void
    {
        add_action('cli_init', array($this, 'registerCommands'));
    }

    public function registerCommands(): void
    {
        foreach ($this->commands as $aCommand) {
            $aCommand->register();
        }
    }
}
