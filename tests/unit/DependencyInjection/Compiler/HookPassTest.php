<?php

namespace Simply\Tests\DependencyInjection\Compiler;

use Simply\Core\Compiler\HookCompiler;
use Simply\Core\DependencyInjection\Compiler\HookPass;
use Simply\Tests\Fixtures\ExampleServiceHookAttribute;
use Simply\Tests\Fixtures\ExampleServiceSubscriberClass;
use Simply\Tests\SimplyTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HookPassTest extends SimplyTestCase
{
    public function testAddContainerServiceSubscriberTagAndAutowired()
    {
        $container = new ContainerBuilder();
        $definition = $container->register('example', ExampleServiceSubscriberClass::class);
        $definition->addTag('wp.hook');

        $hookPass = new HookPass();
        $hookPass->process($container);

        $this->assertTrue(array_key_exists('container.service_subscriber', $definition->getTags()));
        $this->assertTrue($definition->isAutowired());
    }

    public function testAddHooksAndCompile()
    {
        $container = new ContainerBuilder();
        $definition = $container->register(ExampleServiceHookAttribute::class, ExampleServiceHookAttribute::class);

        // Stubs hook compiler in hook pass to not compile
        $hookPass = $this->getMockBuilder(HookPass::class)
            ->onlyMethods(array('getHookCompiler'))
            ->getMock();
        $hookCompiler = $this->getMockBuilder(HookCompiler::class)
            ->onlyMethods(array('compile', 'getFromCache', 'add'))
            ->getMock();

        $hookCompiler->method('getFromCache')->willReturn(array('hook' => 'ok'));
        $hookPass->method('getHookCompiler')->willReturn($hookCompiler);

        $hookPass->process($container);
        $this->assertTrue($definition->hasTag('simply.attribute_hooks'));
        $this->assertSame(array('hook' => 'ok'), $container->getParameter('simply.compile_hooks'));
    }
}
