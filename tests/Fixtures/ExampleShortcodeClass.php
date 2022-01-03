<?php

namespace Simply\Tests\Fixtures;

use Simply\Core\Shortcode\AbstractShortcode;

class ExampleShortcodeClass extends AbstractShortcode {
    public static $itsTag = 'example';

    public function myFunction ($title) {
        return $title;
    }

    public function getDefaultParams() {
    }

    protected function render($atts, $content) {
    }
}
