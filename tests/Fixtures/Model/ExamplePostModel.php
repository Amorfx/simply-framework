<?php

namespace Simply\Tests\Fixtures\Model;

use Simply\Core\Model\PostTypeObject;

class ExamplePostModel extends PostTypeObject {
    public static function getType() {
        return 'example_type';
    }
}
