<?php

namespace Simply\Core\Manager;

use Psr\Container\ContainerInterface;
use Simply\Core\Contract\HookableInterface;
use Simply\Core\Contract\ManagerInterface;
use Simply\Core\Hook\LazyHookListener;

class HookManager implements ManagerInterface
{
    /**
     * @param iterable<HookableInterface> $hooks
     * @param list<array{type: string, hook: string, priority: int, accepted_args: int, service: string, class: class-string, method: string}> $attributeHooks collected by HookPass
     * @param ContainerInterface $attributeHookServices services holding the attribute hooks, by service id
     */
    public function __construct(
        private iterable $hooks,
        private array $attributeHooks,
        private ContainerInterface $attributeHookServices,
    ) {
    }

    public function initialize(): void
    {
        foreach ($this->hooks as $aHook) {
            $aHook->register();
        }

        foreach ($this->attributeHooks as $hook) {
            $listener = new LazyHookListener($this->attributeHookServices, $hook['service'], $hook['class'], $hook['method']);
            if ($hook['type'] === 'action') {
                // The listener returns the method result, which WordPress ignores for actions
                add_action($hook['hook'], $listener, $hook['priority'], $hook['accepted_args']); // @phpstan-ignore return.void
            } else {
                add_filter($hook['hook'], $listener, $hook['priority'], $hook['accepted_args']);
            }
        }
    }
}
