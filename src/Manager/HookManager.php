<?php

namespace SimplyFramework\Manager;

use SimplyFramework\Contract\HookableInterface;
use SimplyFramework\Contract\ManagerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

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
