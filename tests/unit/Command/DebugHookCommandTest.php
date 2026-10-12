<?php

namespace Simply\Tests\Command;

use Simply\Core\Command\DebugHookCommand;
use Simply\Tests\SimplyTestCase;

class DebugHookCommandTest extends SimplyTestCase
{
    public function testCommandIsNamedAndDescribed()
    {
        $command = new DebugHookCommand();

        $this->assertSame('simply:debug:hook', $command->getName());
        $this->assertSame('List all hooks registered in the application.', $command->getDescription());
    }
}
