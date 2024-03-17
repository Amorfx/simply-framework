<?php

namespace Simply\Core\Attributes;

use Attribute;
use Simply\Core\Contract\HookableInterface;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
class Filter implements HookableInterface
{
    private string $filter;
    private int $priority;
    private int $numberArguments;
    /**
     * @var array<object|string, string>
     */
    private array $callable;

    public function __construct(string $filter, int $priority = 10, int $numberArguments = 1)
    {
        $this->filter = $filter;
        $this->priority = $priority;
        $this->numberArguments = $numberArguments;
    }

    public function getHook(): string
    {
        return $this->filter;
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
        add_filter($this->filter, $this->callable, $this->priority, $this->numberArguments);
    }
}
