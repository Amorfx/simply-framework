<?php

namespace Simply\Core\Manager;

use Simply\Core\Contract\ManagerInterface;
use Simply\Core\Shortcode\AbstractShortcode;

class ShortcodeManager implements ManagerInterface {
    /**
     * @var AbstractShortcode[]
     */
    private $shortcodes;

    public function __construct($shortcodes) {
        $this->shortcodes = $shortcodes;
    }

    public function initialize() {
        add_action('init', function() {
            foreach ($this->shortcodes as $aShortcode) {
                if (!$aShortcode instanceof AbstractShortcode) {
                    throw new \RuntimeException('Services with tags wp_shortcode has to be an extension class of AbstractShortcode');
                }

                $aShortcode->register();
            }
        });
    }

    /**
     * Get a shortcode by its tag or classname
     * @param $key
     *
     * @return false|AbstractShortcode
     */
    public function getShortcode($key) {
        foreach ($this->shortcodes as $aShortcode) {
            if ($aShortcode->getTag() === $key || get_class($aShortcode) === $key) {
                return $aShortcode;
            }
        }
         return false;
    }
}
