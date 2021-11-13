<?php

namespace Simply\Core\Attributes;

use Attribute;
use Simply\Core\Contract\HookableInterface;

#[Attribute]
class Action implements HookableInterface {
    private string $action;
    private int $priority;
    private int $numberArguments;
    private array $callable;

    public function __construct(string $action, int $priority = 10, int $numberArguments = 1) {
        $this->action = $action;
        $this->priority = $priority;
        $this->numberArguments = $numberArguments;
    }

    public function getHook() {
        return $this->action;
    }

    public function setCallable(array $callable) {
        $this->callable = $callable;
    }

    public function register() {
        add_action($this->action, $this->callable, $this->priority, $this->numberArguments);
    }
}