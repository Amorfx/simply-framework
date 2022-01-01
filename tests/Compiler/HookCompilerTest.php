<?php

namespace Simply\Tests\Compiler;

use Simply\Core\Compiler\HookCompiler;
use Simply\Tests\SimplyTestCase;

class HookCompilerTest extends SimplyTestCase {
    public function testAddHookAndGet() {
        $hookCompiler = new HookCompiler();
        $hookCompiler->add('myClass', 'Action', 'myHook', 'myFunction');

        // Test in property
        $reflection = new \ReflectionClass($hookCompiler);
        $property = $reflection->getProperty('hooksMapping');
        $property->setAccessible(true);
        $expected = array( 0 => array(
            'hook' => 'myHook',
            'type' => 'Action',
            'fn' => 'myFunction',
            'priority' => 10,
            'numberArguments' => 1,
        ));
        $propertyValue = $property->getValue($hookCompiler);
        $stub = $this->getMockBuilder(HookCompiler::class)
            ->onlyMethods(array('getFromCache'))
            ->getMock();
        $stub->method('getFromCache')
            ->willReturn($propertyValue);
        $this->assertSame($expected, $stub->getFromClass('myClass'));
    }
}
