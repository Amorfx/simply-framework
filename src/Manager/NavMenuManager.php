<?php

namespace Simply\Core\Manager;

use Simply\Core\Contract\ManagerInterface;

class NavMenuManager implements ManagerInterface
{
    /** @var array<string>  */
    private array $navMenus;

    /** @param array<string> $navMenus */
    public function __construct(array $navMenus)
    {
        $this->navMenus = $navMenus;
    }

    public function initialize(): void
    {
        add_action('init', array($this, 'registerMenus'));
    }

    public function registerMenus(): void
    {
        register_nav_menus($this->navMenus);
    }
}
