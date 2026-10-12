<?php

namespace Simply\Tests\Attributes;

use Simply\Core\Attributes\Action;
use Simply\Tests\SimplyTestCase;

class ActionTest extends SimplyTestCase
{
    private Action $attribute;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attribute = new Action('my_action');
    }

    public function testGetHook()
    {
        $this->assertEquals('my_action', $this->attribute->getHook());
    }

    public function testPriorityAndNumberOfArguments()
    {
        $this->assertSame([10, 1], [$this->attribute->getPriority(), $this->attribute->getNumberArguments()]);

        $custom = new Action('my_action', 20, 3);
        $this->assertSame([20, 3], [$custom->getPriority(), $custom->getNumberArguments()]);
    }
}
