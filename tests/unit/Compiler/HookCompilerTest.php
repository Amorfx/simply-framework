<?php

namespace Simply\Tests\Compiler;

use Simply\Core\Compiler\HookCompiler;
use Simply\Tests\SimplyTestCase;

class HookCompilerTest extends SimplyTestCase
{
    public function testGetHooksMappingReturnsAddedHooksByClass()
    {
        $hookCompiler = new HookCompiler();
        $hookCompiler->add('myClass', 'Action', 'myHook', 'myFunction');
        $hookCompiler->add('myClass', 'Filter', 'otherHook', 'otherFunction', 20, 2);

        $this->assertSame(['myClass' => [
            ['hook' => 'myHook', 'type' => 'Action', 'fn' => 'myFunction', 'priority' => 10, 'numberArguments' => 1],
            ['hook' => 'otherHook', 'type' => 'Filter', 'fn' => 'otherFunction', 'priority' => 20, 'numberArguments' => 2],
        ]], $hookCompiler->getHooksMapping());
    }

    public function testGetHooksMappingIsEmptyWithoutHooks()
    {
        $this->assertSame([], (new HookCompiler())->getHooksMapping());
    }
}
