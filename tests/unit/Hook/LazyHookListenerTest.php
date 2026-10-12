<?php

namespace Simply\Tests\Hook;

use Simply\Core\Hook\LazyHookListener;
use Simply\Tests\Fixtures\ExampleLazyHookService;
use Simply\Tests\SimplyTestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;

class LazyHookListenerTest extends SimplyTestCase
{
    private ServiceLocator $services;

    protected function setUp(): void
    {
        parent::setUp();
        ExampleLazyHookService::$instances = 0;
        // A compiled container shares the service: the factory does the same
        $instance = null;
        $this->services = new ServiceLocator(['app.title' => static function () use (&$instance) {
            return $instance ??= new ExampleLazyHookService();
        }]);
    }

    public function testServiceIsNotInstantiatedUntilTheHookRuns()
    {
        new LazyHookListener($this->services, 'app.title', ExampleLazyHookService::class, 'title');

        $this->assertSame(0, ExampleLazyHookService::$instances);
    }

    public function testInvokingCallsTheServiceMethodWithTheArgumentsAndReturnsItsValue()
    {
        $listener = new LazyHookListener($this->services, 'app.title', ExampleLazyHookService::class, 'title');

        $this->assertSame('Hello #42', $listener('Hello', 42));
        $this->assertSame('Again #1', $listener('Again', 1));
        $this->assertSame(1, ExampleLazyHookService::$instances);
    }

    public function testExposesTheTargetForDebugging()
    {
        $listener = new LazyHookListener($this->services, 'app.title', ExampleLazyHookService::class, 'title');

        $this->assertSame(['app.title', ExampleLazyHookService::class, 'title'], [$listener->serviceId, $listener->class, $listener->method]);
    }
}
