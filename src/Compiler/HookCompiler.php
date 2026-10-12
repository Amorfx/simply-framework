<?php

namespace Simply\Core\Compiler;

class HookCompiler
{
    /**
     * @var array<string, array<array<string, string|int>>>
     */
    private array $hooksMapping = array();

    public function add(string $className, string $hookClass, string $hook, string $function, int $priority = 10, int $numberArguments = 1): void
    {
        /** @phpstan-ignore-next-line  */
        if (!isset($this->hooksMapping[$className]) || !is_array($this->hooksMapping[$className])) {
            $this->hooksMapping[$className] = array();
        }
        $this->hooksMapping[$className][] = array(
            'hook' => $hook,
            'type' => $hookClass,
            'fn' => $function,
            'priority' => $priority,
            'numberArguments' => $numberArguments,
        );
    }

    /**
     * @return array<string, array<array<string, string|int>>>
     */
    public function getHooksMapping(): array
    {
        return $this->hooksMapping;
    }
}
