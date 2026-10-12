<?php

namespace Simply\Tests\Fixtures;

use Simply\Core\Attributes\Action;

class ExampleByReferenceHookService
{
    #[Action('my_ref_action')]
    public function addItem(array &$items): void
    {
        $items[] = 'added';
    }
}
