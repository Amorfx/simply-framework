<?php

namespace Simply\Tests\Manager;

use Simply\Core\Manager\TaxonomyManager;
use Simply\Tests\SimplyTestCase;
use Brain\Monkey;

class TaxonomyManagerTest extends SimplyTestCase {
    public function testInitialize() {
        $manager = new TaxonomyManager(array());
        $manager->initialize();
        $this->assertEquals(10, has_action('init', TaxonomyManager::class . '->registerTaxonomies()'));
    }

    public function testRegisterTaxonomies() {
        $manager = new TaxonomyManager(array('taxonomy' => array('object_type' => 'post', 'args' => array('ok'))));
        Monkey\Functions\expect('register_taxonomy')->once()->with('taxonomy', 'post', array('ok'));
        $manager->registerTaxonomies();

        $manager = new TaxonomyManager(array('other_tax' => array('object_type' => 'post')));
        Monkey\Functions\expect('register_taxonomy')->once()->with('other_tax', 'post', array());
        $manager->registerTaxonomies();
    }
}
