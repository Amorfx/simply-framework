<?php

namespace Simply\Core\Manager;

use Simply\Core\Contract\ManagerInterface;

class NavMenuManager implements ManagerInterface {
    private $navMenus;

    public function __construct(array $navMenus) {
        $this->navMenus = $navMenus;
    }

    public function initialize() {
        add_action('init', array($this, 'registerMenus'));
    }

    public function registerMenus() {
        register_nav_menus($this->navMenus);
    }
}
