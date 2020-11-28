<?php

namespace Simply\Core\Manager;

use Simply\Core\Contract\HookableInterface;
use Simply\Core\Contract\ManagerInterface;

class HookManager implements ManagerInterface {
    /**
     * @var HookableInterface[]
     */
    private $hooks;

    public function __construct($hooks) {
        $this->hooks = $hooks;
    }

    public function initialize() {
        foreach ($this->hooks as $aHook) {
            $aHook->register();
        }
    }
}
