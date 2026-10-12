<?php

namespace Simply\Tests\Fixtures;

use Simply\Core\Attributes\Action;

class ExamplePrivateHookService
{
    #[Action('init')]
    private function hidden(): void
    {
    }
}
