<?php

namespace Simply\Tests\Fixtures;

use Simply\Core\Attributes\Filter;

class ExampleLazyHookService
{
    public static int $instances = 0;

    public function __construct()
    {
        self::$instances++;
    }

    #[Filter('the_title', 20, 2)]
    public function title(string $title, int $postId): string
    {
        return $title . ' #' . $postId;
    }
}
