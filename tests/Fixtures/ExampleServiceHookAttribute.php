<?php

namespace Simply\Tests\Fixtures;

use Simply\Core\Attributes\Action;

class ExampleServiceHookAttribute
{
    #[Action('init')]
    public function myInit()
    {
    }
}
