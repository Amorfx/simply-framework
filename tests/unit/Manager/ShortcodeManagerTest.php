<?php

namespace Simply\Tests\Manager;

use Simply\Core\Manager\ShortcodeManager;
use Simply\Core\Shortcode\AbstractShortcode;
use Simply\Tests\Fixtures\ExampleClass;
use Simply\Tests\Fixtures\ExampleShortcodeClass;
use Simply\Tests\SimplyTestCase;

class ShortcodeManagerTest extends SimplyTestCase
{
    public function testInitializeAddAction()
    {
        $manager = new ShortcodeManager(array());
        $manager->initialize();
        $this->assertSame(10, has_action('init', ShortcodeManager::class . '->registerShortcodes()'));
    }

    public function testRegisterWithException()
    {
        $manager = new ShortcodeManager(array(new ExampleClass()));
        $this->expectException(\RuntimeException::class);
        $manager->registerShortcodes();
    }

    public function testRegisterOk()
    {
        $shortcode = $this->getMockBuilder(AbstractShortcode::class)
            ->onlyMethods(array('register'))
            ->getMockForAbstractClass();
        $shortcode->expects($this->once())->method('register');
        $manager = new ShortcodeManager(array($shortcode));
        $manager->registerShortcodes();
    }

    public function testGetShortcode()
    {
        $manager = new ShortcodeManager(array(new ExampleShortcodeClass()));
        $this->assertFalse($manager->getShortcode('a'));

        $this->assertInstanceOf(ExampleShortcodeClass::class, $manager->getShortcode('example'));
        $this->assertInstanceOf(ExampleShortcodeClass::class, $manager->getShortcode(ExampleShortcodeClass::class));
    }
}
