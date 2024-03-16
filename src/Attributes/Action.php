<?php

namespace Simply\Core\Attributes;

use Attribute;
use Simply\Core\Contract\HookableInterface;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
class Action implements HookableInterface
{
    private string $action;
    private int $priority;
    private int $numberArguments;
    /**
     * @var array<object|string, string> $callable
     */
    private array $callable;

    public function __construct(string $action, int $priority = 10, int $numberArguments = 1)
    {
        $this->action = $action;
        $this->priority = $priority;
        $this->numberArguments = $numberArguments;
    }

    public function getHook(): string
    {
        return $this->action;
    }

    /**
     * @param array<object|string, string> $callable
     * @return void
     */
    public function setCallable(array $callable): void
    {
        $this->callable = $callable;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return int
     */
    public function getNumberArguments(): int
    {
        return $this->numberArguments;
    }

    public function register(): void
    {
        add_action($this->action, $this->callable, $this->priority, $this->numberArguments);
    }
}
