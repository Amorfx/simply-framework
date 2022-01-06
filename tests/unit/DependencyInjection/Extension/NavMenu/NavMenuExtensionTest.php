<?php

namespace Simply\Tests\DependencyInjection\Extension\NavMenu;

use Simply\Core\DependencyInjection\Extension\NavMenu\NavMenuExtension;
use Simply\Tests\SimplyTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Brain\Monkey;

class NavMenuExtensionTest extends SimplyTestCase {
    public function testAddNavMenuParam() {
        $container = new ContainerBuilder();
        $extension = new NavMenuExtension();
        $configs = array(
            array('my_menu' => 'trans(ok, mydomain)')
        );
        Monkey\Functions\when('__')->justReturn('translated');
        $extension->load($configs, $container);
        $this->assertSame(array('my_menu' => 'translated'), $container->getParameter('nav_menu'));
    }

    public function testGetAlias() {
        $extension = new NavMenuExtension();
        $this->assertEquals('nav_menu', $extension->getAlias());
    }

    public function testGetNamespace() {
        $extension = new NavMenuExtension();
        $this->assertFalse($extension->getNamespace());
    }

    public function testGetXsdValidationBasePath() {
        $extension = new NavMenuExtension();
        $this->assertNull( $extension->getXsdValidationBasePath());
    }
}
