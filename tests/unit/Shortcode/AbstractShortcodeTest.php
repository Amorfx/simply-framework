<?php

namespace Simply\Tests\Shortcode;

use Brain\Monkey;
use Simply\Core\Shortcode\AbstractShortcode;
use Simply\Tests\SimplyTestCase;

class AbstractShortcodeTest extends SimplyTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Same contract as WordPress: only known attributes are kept, defaults fill the rest
        Monkey\Functions\when('shortcode_atts')->alias(
            static fn(array $pairs, array $atts): array => array_merge($pairs, array_intersect_key($atts, $pairs))
        );
    }

    private function createShortcode(): AbstractShortcode
    {
        return new class extends AbstractShortcode {
            public static string $itsTag = 'test';

            public function getDefaultParams(): array
            {
                return ['title' => 'default'];
            }

            protected function render(array $atts, $content): mixed
            {
                return $atts;
            }
        };
    }

    public function testShortcodeWithoutAttributesUsesDefaults(): void
    {
        $this->assertSame(['title' => 'default'], $this->createShortcode()->actionShortcode(''));
    }

    public function testShortcodeWithAttributes(): void
    {
        $this->assertSame(['title' => 'x'], $this->createShortcode()->actionShortcode(['title' => 'x', 'unknown' => 'y']));
    }
}
