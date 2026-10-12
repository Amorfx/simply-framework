<?php

namespace Simply\Tests\DependencyInjection\Compiler;

use Brain\Monkey;
use Simply\Core\Hook\LazyHookListener;
use Simply\Core\Manager\HookManager;
use Simply\Core\DependencyInjection\Compiler\HookPass;
use Simply\Tests\Fixtures\ExampleByReferenceHookService;
use Simply\Tests\Fixtures\ExampleLazyHookService;
use Simply\Tests\Fixtures\ExamplePrivateHookService;
use Simply\Tests\Fixtures\ExampleServiceHookAttribute;
use Simply\Tests\Fixtures\ExampleServiceMethodsSubscriberClass;
use Simply\Tests\Fixtures\ExampleServiceSubscriberClass;
use Simply\Tests\SimplyTestCase;
use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
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

    public function testServiceMethodsSubscriberTraitIsTaggedAndAutowired()
    {
        $container = new ContainerBuilder();
        $definition = $container->register('example', ExampleServiceMethodsSubscriberClass::class);
        $definition->addTag('wp.hook');

        (new HookPass())->process($container);

        $this->assertTrue($definition->hasTag('container.service_subscriber'));
        $this->assertTrue($definition->isAutowired());
    }

    private function containerWithHookManager(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->register(HookPass::HOOK_MANAGER, HookManager::class)
            ->setPublic(true)
            ->setArguments([new TaggedIteratorArgument('wp.hook'), [], new ServiceLocatorArgument([])]);

        return $container;
    }

    public function testAttributeHooksAreCollectedByServiceId()
    {
        $container = $this->containerWithHookManager();
        $container->register('app.hooks', ExampleServiceHookAttribute::class);

        (new HookPass())->process($container);

        $manager = $container->getDefinition(HookPass::HOOK_MANAGER);
        $this->assertSame([[
            'type' => 'action', 'hook' => 'init', 'priority' => 10, 'accepted_args' => 1,
            'service' => 'app.hooks', 'class' => ExampleServiceHookAttribute::class, 'method' => 'myInit',
        ]], $manager->getArgument(1));
        $this->assertArrayHasKey('app.hooks', $container->getDefinition((string) $manager->getArgument(2))->getArgument(0));
    }

    public function testTwoServicesOfTheSameClassBothGetTheirHooks()
    {
        $container = $this->containerWithHookManager();
        $container->register('first', ExampleServiceHookAttribute::class);
        $container->register('second', ExampleServiceHookAttribute::class);

        (new HookPass())->process($container);

        $this->assertSame(['first', 'second'], array_column($container->getDefinition(HookPass::HOOK_MANAGER)->getArgument(1), 'service'));
    }

    public function testAbstractDefinitionsAreIgnored()
    {
        $container = $this->containerWithHookManager();
        $container->register('parent', ExampleServiceHookAttribute::class)->setAbstract(true);

        (new HookPass())->process($container);

        $this->assertSame([], $container->getDefinition(HookPass::HOOK_MANAGER)->getArgument(1));
    }

    public function testNonPublicAttributeMethodFailsTheBuild()
    {
        $container = $this->containerWithHookManager();
        $container->register(ExamplePrivateHookService::class, ExamplePrivateHookService::class);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('"Simply\Tests\Fixtures\ExamplePrivateHookService::hidden()" has a #[Action] attribute but is not public');

        (new HookPass())->process($container);
    }

    public function testByReferenceParameterFailsTheBuild()
    {
        // The lazy listener receives arguments by value: a by-reference parameter would silently stop working
        $container = $this->containerWithHookManager();
        $container->register(ExampleByReferenceHookService::class, ExampleByReferenceHookService::class);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('"Simply\Tests\Fixtures\ExampleByReferenceHookService::addItem()" takes $items by reference');

        (new HookPass())->process($container);
    }

    public function testWithoutHookManagerNothingFails()
    {
        $container = new ContainerBuilder();
        $container->register('app.hooks', ExampleServiceHookAttribute::class);

        (new HookPass())->process($container);

        $this->assertFalse($container->hasParameter('simply.compile_hooks'));
    }

    public function testCompiledContainerRegistersHooksWithoutInstantiatingPrivateServices()
    {
        ExampleLazyHookService::$instances = 0;
        $container = $this->containerWithHookManager();
        // No tagged iterator here: Patchwork (Brain Monkey) breaks the first-class callable syntax of RewindableGenerator
        $container->getDefinition(HookPass::HOOK_MANAGER)->replaceArgument(0, []);
        $container->register('app.title', ExampleLazyHookService::class)->setPublic(false);
        $container->addCompilerPass(new HookPass());
        $container->compile();

        Monkey\Filters\expectAdded('the_title')->once()->with(\Mockery::type(LazyHookListener::class), 20, 2);
        $container->get(HookPass::HOOK_MANAGER)->initialize();

        $this->assertSame(0, ExampleLazyHookService::$instances);
    }
}
