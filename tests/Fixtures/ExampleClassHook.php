<?php

namespace Simply\Tests\Fixtures;

final class ExampleClassHook
{
    public function __construct()
    {
        add_action('my_hook', [$this, 'functionInit']);
        add_action('hook_with_function', [$this, 'otherFunction']);
        add_action('other_hook', [$this, 'otherFunction']);
    }

    public function functionInit(): void
    {
        // do something
    }

    public function otherFunction(): void
    {
        // do something
    }
}
