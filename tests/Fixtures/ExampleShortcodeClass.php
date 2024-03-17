<?php

namespace Simply\Tests\Fixtures;

use Simply\Core\Shortcode\AbstractShortcode;

class ExampleShortcodeClass extends AbstractShortcode
{
    public static string $itsTag = 'example';

    public function myFunction($title)
    {
        return $title;
    }

    public function getDefaultParams(): array
    {
        return [];
    }

    protected function render($atts, $content): mixed
    {
        return '';
    }
}
