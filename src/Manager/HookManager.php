<?php

namespace Simply\Core\Manager;

use Simply\Core\Contract\HookableInterface;
use Simply\Core\Contract\ManagerInterface;

class HookManager implements ManagerInterface
{
    /**
     * @var array<HookableInterface>
     */
    private iterable $hooks;

    /**
     * @var array<string, array<array<string, string|int>>>
     */
    private array $compileHooks;
    /**
     * @var array<object>
     */
    private iterable $attributeHooksService;

    /**
     * @param array<HookableInterface> $hooks
     * @param array<string, array<array<string, string|int>>> $compileHooks
     * @param array<object> $attributeHooksService
     */
    public function __construct(iterable $hooks, array $compileHooks, iterable $attributeHooksService)
    {
        $this->hooks = $hooks;
        $this->compileHooks = $compileHooks;
        $this->attributeHooksService = $attributeHooksService;
    }

    public function initialize(): void
    {
        foreach ($this->hooks as $aHook) {
            $aHook->register();
        }

        if (!empty($this->compileHooks)) {
            foreach ($this->compileHooks as $class => $hooks) {
                foreach ($hooks as $arrayHook) {
                    $attributeHook = new $arrayHook['type'](
                        $arrayHook['hook'],
                        $arrayHook['priority'],
                        $arrayHook['numberArguments']
                    );

                    $service = $this->getServiceFromClass($class);
                    if (false === $service) {
                        throw new \Exception('The service ' . $class . ' is not register in container.');
                    }
                    $attributeHook->setCallable(array($service, $arrayHook['fn']));
                    if (is_callable(array($service, $arrayHook['fn']))) {
                        $attributeHook->register();
                    }
                }
            }
        }
    }

    /**
     * In compile hook we have the classname not the service
     * so we have to search the service instance with the classname
     * @param string $class
     *
     * @return false|object
     */
    private function getServiceFromClass(string $class): object|bool
    {
        foreach ($this->attributeHooksService as $service) {
            if ($service instanceof $class) {
                return $service;
            }
        }
        return false;
    }
}
