<?php

namespace Simply\Core\Manager;

use Simply\Core\Contract\ManagerInterface;
use Simply\Core\Shortcode\AbstractShortcode;

class ShortcodeManager implements ManagerInterface
{
    /**
     * @var AbstractShortcode[]
     */
    private iterable $shortcodes;

    /**
     * @param AbstractShortcode[] $shortcodes
     */
    public function __construct(iterable $shortcodes)
    {
        $this->shortcodes = $shortcodes;
    }

    public function initialize(): void
    {
        add_action('init', array($this, 'registerShortcodes'));
    }

    public function registerShortcodes(): void
    {
        foreach ($this->shortcodes as $aShortcode) {
            if (!$aShortcode instanceof AbstractShortcode) {
                throw new \RuntimeException('Services with tags wp_shortcode has to be an extension class of AbstractShortcode');
            }

            $aShortcode->register();
        }
    }

    /**
     * Get a shortcode by its tag or classname
     *
     * @return false|AbstractShortcode
     */
    public function getShortcode(string $key): bool|AbstractShortcode
    {
        foreach ($this->shortcodes as $aShortcode) {
            if ($aShortcode->getTag() === $key || get_class($aShortcode) === $key) {
                return $aShortcode;
            }
        }
        return false;
    }
}
