<?php

namespace Simply\Tests\Manager;

use Simply\Core\Manager\NavMenuManager;
use Simply\Tests\SimplyTestCase;
use Brain\Monkey;

class NavMenuManagerTest extends SimplyTestCase {
    public function testInitialize() {
        $manager = new NavMenuManager(array('menu'));
        $manager->initialize();
        // Expect added cli init
        $this->assertSame(10, has_action('init', NavMenuManager::class . '->registerMenus()'));

        Monkey\Functions\expect('register_nav_menus')->with(array('menu'));
        $manager->registerMenus();
    }
}
