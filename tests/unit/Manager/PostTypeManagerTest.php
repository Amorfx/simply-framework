<?php

namespace Simply\Tests\Manager;

use Simply\Core\Manager\PostTypeManager;
use Simply\Tests\SimplyTestCase;
use Brain\Monkey;

class PostTypeManagerTest extends SimplyTestCase {
    public function testInitialize() {
        $manager = new PostTypeManager(array('post_type' => array('ok')));
        $manager->initialize();
        // Expect added cli init
        $this->assertSame(10, has_action('init', PostTypeManager::class . '->registerPostTypes()'));

        Monkey\Functions\expect('register_post_type')->once()->with('post_type', array('ok'));
        $manager->registerPostTypes();
    }
}
