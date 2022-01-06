<?php

namespace Simply\Tests\Attributes;

use Simply\Core\Attributes\Action;
use Simply\Tests\Fixtures\ExampleClass;
use Simply\Tests\SimplyTestCase;
use Brain\Monkey;

class ActionTest extends SimplyTestCase {
    private Action $attribute;

    protected function setUp(): void {
        parent::setUp();
        $this->attribute = new Action('my_action');
    }

    public function testGetHook() {
        $this->assertEquals('my_action', $this->attribute->getHook());
    }

    public function testRegister() {
        $exampleClass = new ExampleClass();
        $this->attribute->setCallable(array($exampleClass, 'myFunction'));
        Monkey\Actions\expectAdded('my_action')->once()->with(array($exampleClass, 'myFunction'), 10, 1);
        $this->attribute->register();
    }
}
