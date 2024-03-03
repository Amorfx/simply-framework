<?php

namespace Simply\Tests\DependencyInjection\Extension\NavMenu;

use Simply\Core\DependencyInjection\Extension\PostType\PostTypeExtension;
use Simply\Tests\SimplyTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Brain\Monkey;

class PostTypeExtensionTest extends SimplyTestCase
{
    public function testAddNavMenuParam()
    {
        $container = new ContainerBuilder();
        $extension = new PostTypeExtension();
        $configs = array(
            array('my_cpt' => array(
                'public' => true,
                'labels' => 'trans(ok, mydomain)'
            ))
        );
        Monkey\Functions\when('__')->justReturn('translated');
        $extension->load($configs, $container);
        $this->assertSame(array('my_cpt' => array('public' => true, 'labels' => 'translated')), $container->getParameter('post_type'));
    }

    public function testGetAlias()
    {
        $extension = new PostTypeExtension();
        $this->assertEquals('post_type', $extension->getAlias());
    }

    public function testGetNamespace()
    {
        $extension = new PostTypeExtension();
        $this->assertFalse($extension->getNamespace());
    }

    public function testGetXsdValidationBasePath()
    {
        $extension = new PostTypeExtension();
        $this->assertFalse($extension->getXsdValidationBasePath());
    }
}
