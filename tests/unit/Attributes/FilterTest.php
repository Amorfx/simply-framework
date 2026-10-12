<?php

namespace Simply\Tests\Attributes;

use Simply\Core\Attributes\Filter;
use Simply\Tests\SimplyTestCase;

class FilterTest extends SimplyTestCase
{
    private Filter $attribute;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attribute = new Filter('my_filter');
    }

    public function testGetHook()
    {
        $this->assertEquals('my_filter', $this->attribute->getHook());
    }

    public function testPriorityAndNumberOfArguments()
    {
        $this->assertSame([10, 1], [$this->attribute->getPriority(), $this->attribute->getNumberArguments()]);

        $custom = new Filter('my_filter', 20, 3);
        $this->assertSame([20, 3], [$custom->getPriority(), $custom->getNumberArguments()]);
    }
}
