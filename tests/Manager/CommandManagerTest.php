<?php

namespace Simply\Tests\Manager;

use Simply\Core\Command\AbstractWordPressCommand;
use Simply\Core\Manager\CommandManager;
use Simply\Tests\SimplyTestCase;
use Brain\Monkey;

class CommandManagerTest extends SimplyTestCase {
    public function testInitialize() {
        $command = $this->getMockBuilder(AbstractWordPressCommand::class)->onlyMethods(array('register'))->getMockForAbstractClass();
        $command2 = $this->getMockBuilder(AbstractWordPressCommand::class)->onlyMethods(array('register'))->getMockForAbstractClass();
        $command->expects($this->once())->method('register');
        $command2->expects($this->once())->method('register');
        $commands = array($command, $command2);
        $manager = new CommandManager($commands);
        $manager->initialize();

        // Expect added cli init
        $this->assertSame(10, has_action('cli_init', CommandManager::class . '->registerCommands()'));
        // Test expects register
        $manager->registerCommands();
    }
}
