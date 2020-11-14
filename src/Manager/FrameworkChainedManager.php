<?php

namespace SimplyFramework\Manager;

use SimplyFramework\Contract\ManagerInterface;

// TODO add manager NavMenu
class FrameworkChainedManager implements ManagerInterface {
    /**
     * @var ManagerInterface[]
     */
    private $allManagers;
    
    public function __construct($allManagers) {
        $this->allManagers = $allManagers;
    }

    public function initialize() {
        foreach ($this->allManagers as $aManager) {
            $aManager->initialize();
        }
    }
}
