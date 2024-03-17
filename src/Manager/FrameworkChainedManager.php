<?php

namespace Simply\Core\Manager;

use Simply\Core\Contract\ManagerInterface;

class FrameworkChainedManager implements ManagerInterface
{
    /**
     * @var ManagerInterface[]
     */
    private iterable $allManagers;

    /**
     * @param ManagerInterface[] $allManagers
     */
    public function __construct(iterable $allManagers)
    {
        $this->allManagers = $allManagers;
    }

    public function initialize(): void
    {
        foreach ($this->allManagers as $aManager) {
            $aManager->initialize();
        }
    }
}
