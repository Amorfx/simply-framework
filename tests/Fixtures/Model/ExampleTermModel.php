<?php

namespace Simply\Tests\Fixtures\Model;

use Simply\Core\Model\TermObject;

class ExampleTermModel extends TermObject
{
    public static function getType(): string
    {
        return 'example_type';
    }
}
