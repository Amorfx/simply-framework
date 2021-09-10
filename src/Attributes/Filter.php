<?php

namespace Simply\Core\Attributes;

use Attribute;
use Simply\Core\Contract\HookableInterface;

#[Attribute]
class Filter implements HookableInterface {
    private string $filter;
    private int $priority;
    private int $numberArguments;
    private array $callable;

    public function __construct(string $filter, int $priority = 10, int $numberArguments = 1) {
        $this->filter = $filter;
        $this->priority = $priority;
        $this->numberArguments = $numberArguments;
    }

    public function getHook() {
        return $this->filter;
    }

    public function setCallable(array $callable) {
        $this->callable = $callable;
    }

    public function register() {
        add_filter($this->filter, $this->callable, $this->priority, $this->numberArguments);
    }
}
