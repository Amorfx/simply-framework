<?php

namespace Simply\Tests\DependencyInjection\Extension\NavMenu;

use Simply\Core\DependencyInjection\Extension\PostType\PostTypeExtension;
use Simply\Core\DependencyInjection\Extension\Taxonomy\TaxonomyExtension;
use Simply\Tests\SimplyTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Brain\Monkey;

class TaxonomyExtensionTest extends SimplyTestCase
{
    public function testAddNavMenuParam()
    {
        $container = new ContainerBuilder();
        $extension = new TaxonomyExtension();
        $configs = array(
            array(
                'my_tax' => array(
                    'object_type' => 'post',
                    'args' => array('labels' => 'trans(ok, mydomain)'),
                ),
            ),
        );
        Monkey\Functions\when('__')->justReturn('translated');
        $extension->load($configs, $container);
        $this->assertSame(array(
            'my_tax' => array(
                'object_type' => 'post',
                'args' => array('labels' => 'translated'),
            ),
        ), $container->getParameter('taxonomy'));
    }

    public function testGetAlias()
    {
        $extension = new TaxonomyExtension();
        $this->assertEquals('taxonomy', $extension->getAlias());
    }

    public function testGetNamespace()
    {
        $extension = new TaxonomyExtension();
        $this->assertFalse($extension->getNamespace());
    }

    public function testGetXsdValidationBasePath()
    {
        $extension = new TaxonomyExtension();
        $this->assertNull($extension->getXsdValidationBasePath());
    }
}
