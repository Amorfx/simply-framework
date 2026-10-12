<?php

namespace Simply\Tests\Manager;

use Brain\Monkey;
use Simply\Core\Contract\HookableInterface;
use Simply\Core\Hook\LazyHookListener;
use Simply\Core\Manager\HookManager;
use Simply\Tests\Fixtures\ExampleLazyHookService;
use Simply\Tests\SimplyTestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;

class HookManagerTest extends SimplyTestCase
{
    public function testHookableServicesAreRegistered()
    {
        $hookable = \Mockery::mock(HookableInterface::class);
        $hookable->shouldReceive('register')->once();

        (new HookManager([$hookable], [], new ServiceLocator([])))->initialize();
    }

    public function testAttributeHooksAreAddedAsLazyListeners()
    {
        ExampleLazyHookService::$instances = 0;
        $hooks = [
            ['type' => 'filter', 'hook' => 'the_title', 'priority' => 20, 'accepted_args' => 2, 'service' => 'app.title', 'class' => ExampleLazyHookService::class, 'method' => 'title'],
            ['type' => 'action', 'hook' => 'init', 'priority' => 5, 'accepted_args' => 1, 'service' => 'app.title', 'class' => ExampleLazyHookService::class, 'method' => 'title'],
        ];
        $services = new ServiceLocator(['app.title' => static fn() => new ExampleLazyHookService()]);

        Monkey\Filters\expectAdded('the_title')->once()->with(\Mockery::type(LazyHookListener::class), 20, 2);
        Monkey\Actions\expectAdded('init')->once()->with(\Mockery::type(LazyHookListener::class), 5, 1);

        (new HookManager([], $hooks, $services))->initialize();

        $this->assertSame(0, ExampleLazyHookService::$instances);
    }
}
