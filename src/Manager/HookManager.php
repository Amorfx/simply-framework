<?php

namespace Simply\Core\Manager;

use Simply\Core\Contract\HookableInterface;
use Simply\Core\Contract\ManagerInterface;

class HookManager implements ManagerInterface
{
    /**
     * @var array<HookableInterface>
     */
    private $hooks;

    private array $compileHooks;
    /**
     * @var array<object>
     */
    private $attributeHooksService;

    public function __construct($hooks, array $compileHooks, $attributeHooksService)
    {
        $this->hooks = $hooks;
        $this->compileHooks = $compileHooks;
        $this->attributeHooksService = $attributeHooksService;
    }

    public function initialize()
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
    private function getServiceFromClass(string $class)
    {
        foreach ($this->attributeHooksService as $service) {
            if ($service instanceof $class) {
                return $service;
            }
        }
        return false;
    }
}
