<?php

/**
 * Minimal WP_Query test double: records the arguments of the last query.
 */
class WP_Query
{
    /** @var array<string, mixed> */
    public static array $lastArgs = [];

    /** @var array<int, mixed> */
    public array $posts = [];

    /**
     * @param array<string, mixed> $args
     */
    public function __construct(array $args = [])
    {
        self::$lastArgs = $args;
    }
}
