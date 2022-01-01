<?php

namespace Simply\Tests\Attributes;

use Simply\Core\Attributes\Filter;
use Simply\Tests\Fixtures\ExampleClass;
use Simply\Tests\SimplyTestCase;
use Brain\Monkey;

class FilterTest extends SimplyTestCase {
    private Filter $attribute;

    protected function setUp(): void {
        parent::setUp();
        $this->attribute = new Filter('my_filter');
    }

    public function testGetHook() {
        $this->assertEquals('my_filter', $this->attribute->getHook());
    }

    public function testRegister() {
        $exampleClass = new ExampleClass();
        $this->attribute->setCallable(array($exampleClass, 'myFunction'));
        Monkey\Filters\expectAdded('my_filter')->once()->with(array($exampleClass, 'myFunction'), 10, 1);
        $this->attribute->register();
    }
}
