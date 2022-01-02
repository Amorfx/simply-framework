<?php

namespace Simply\Tests\Manager;

use Simply\Core\Contract\ManagerInterface;
use Simply\Core\Manager\FrameworkChainedManager;
use Simply\Tests\SimplyTestCase;

class FrameworkChainedManagerTest extends SimplyTestCase {
    public function testInitialize() {
        $manager = $this->createMock(ManagerInterface::class);
        $manager->expects($this->exactly(2))->method('initialize');
        $all = array($manager, $manager);
        $frameworkManager = new FrameworkChainedManager($all);
        $frameworkManager->initialize();
    }
}
