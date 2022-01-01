<?php

namespace Simply\Tests\Attributes;

use Simply\Core\Attributes\Action;
use Simply\Tests\Fixtures\ExampleClass;
use Simply\Tests\SimplyTestCase;

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
        $this->attribute->register();
        $this->assertTrue(has_action('my_action', array($exampleClass, 'myFunction')));
    }
}
