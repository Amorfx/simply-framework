<?php

namespace Simply\Tests\Fixtures\Model;

use Simply\Core\Model\HasCachedProperties;
use Simply\Core\Model\PostTypeObject;

/**
 * @property string $title
 */
class ExamplePostModelCachedProperties extends PostTypeObject
{
    use HasCachedProperties;

    public function getTitle(): string
    {
        return $this->post->post_title;
    }

    public static function getType(): string
    {
        return 'example_type';
    }
}
