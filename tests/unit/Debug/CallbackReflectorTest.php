<?php

namespace Simply\Tests\Debug;

use Simply\Core\Debug\CallbackReflector;
use Simply\Core\Hook\LazyHookListener;
use Simply\Tests\Fixtures\ExampleLazyHookService;
use Simply\Tests\SimplyTestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;

class CallbackReflectorTest extends SimplyTestCase
{
    public function testLazyListenerResolvesToTheServiceMethod()
    {
        ExampleLazyHookService::$instances = 0;
        $listener = new LazyHookListener(new ServiceLocator([]), 'app.title', ExampleLazyHookService::class, 'title');

        $reflection = CallbackReflector::reflect($listener);

        $this->assertInstanceOf(\ReflectionMethod::class, $reflection);
        $this->assertStringEndsWith('tests/Fixtures/ExampleLazyHookService.php', $reflection->getFileName());
        $this->assertSame(ExampleLazyHookService::class . ':title', CallbackReflector::name($listener));
        $this->assertSame(0, ExampleLazyHookService::$instances, 'Debugging must not instantiate the service');
    }

    public function testArrayCallback()
    {
        $this->assertSame(ExampleLazyHookService::class . ':title', CallbackReflector::name([new ExampleLazyHookService(), 'title']));
    }

    public function testStaticMethodString()
    {
        $this->assertSame(CallbackReflector::class . ':name', CallbackReflector::name(CallbackReflector::class . '::name'));
    }

    public function testClosureHasItsSource()
    {
        $reflection = CallbackReflector::reflect(static fn() => null);

        $this->assertSame(__FILE__, $reflection->getFileName());
    }

    public function testInternalFunctionHasNoSource()
    {
        $this->assertSame('trim', CallbackReflector::name('trim'));
        $this->assertFalse(CallbackReflector::reflect('trim')->getFileName());
    }

    public function testInvokableObject()
    {
        $invokable = new class () {
            public function __invoke(): void
            {
            }
        };

        $this->assertStringEndsWith(':__invoke', CallbackReflector::name($invokable));
    }

    public function testUnresolvableCallback()
    {
        $this->assertNull(CallbackReflector::reflect('simply_function_that_does_not_exist'));
        $this->assertSame('', CallbackReflector::name('simply_function_that_does_not_exist'));
    }
}
