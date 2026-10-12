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

    public function testShortcodeWithAttributes(): void
    {
        $this->assertSame(['title' => 'x'], $this->createShortcode()->actionShortcode(['title' => 'x', 'unknown' => 'y']));
    }

    public function testRegisteredCallbackAcceptsEmptyStringAttributes(): void
    {
        $callback = null;
        Monkey\Functions\expect('add_shortcode')
            ->once()
            ->andReturnUsing(static function (string $tag, callable $registered) use (&$callback): void {
                $callback = $registered;
            });

        $this->createShortcode()->register();

        $this->assertSame(['title' => 'default'], $callback(''));
    }

    /**
     * Subclasses overriding actionShortcode(array $atts, ...) must stay compatible
     */
    public function testActionShortcodeSignatureStaysArray(): void
    {
        $parameter = (new \ReflectionMethod(AbstractShortcode::class, 'actionShortcode'))->getParameters()[0];

        $this->assertSame('array', (string) $parameter->getType());
    }
}
