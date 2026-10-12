<?php

namespace Simply\Core\Attributes;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
class Filter
{
    private string $filter;
    private int $priority;
    private int $numberArguments;

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
}
