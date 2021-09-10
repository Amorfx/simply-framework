<?php

namespace Simply\Core\Manager;

use ClementCore\Hook\Excerpt;
use Simply\Core\Attributes\Action;
use Simply\Core\Attributes\Filter;
use Simply\Core\Compiler\HookCompiler;
use Simply\Core\Contract\HookableInterface;
use Simply\Core\Contract\ManagerInterface;

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

        if ($aHook instanceof Excerpt) {
            $hookCompiler = new HookCompiler();
            $compiledHooks = $hookCompiler->getFromCache();
            if ($compiledHooks === false) {
                $ref = new \ReflectionClass($aHook);
                foreach ($ref->getMethods() as $method) {
                    $actionsAttribute = $method->getAttributes(Action::class);
                    $filtersAttribute = $method->getAttributes(Filter::class);
                    $attributes = array_merge($actionsAttribute, $filtersAttribute);
                    if (empty($attributes)) {
                        continue;
                    }
                    foreach ($attributes as $attr) {
                        /** @var Action|Filter $hooks */
                        $hooks = $attr->newInstance();
                        $hooks->setCallable(array($aHook, $method->getName()));
                        $hooks->register();
                        $hookCompiler->add(get_class($aHook), get_class($hooks), $hooks->getHook(), $method->getName());
                    }
                }
                $hookCompiler->compile();
            } else {
                $hooks = $hookCompiler->getFromClass(get_class($aHook));
                foreach ($hooks as $arrayHook) {
                    $attributeHook = new $arrayHook['type'](
                        $arrayHook['hook'],
                        $arrayHook['priority'],
                        $arrayHook['numberArguments']);
                    $attributeHook->setCallable(array($aHook, $arrayHook['fn']));
                    $attributeHook->register();
                }
            }
        }
    }
}
