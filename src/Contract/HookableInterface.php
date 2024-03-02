<?php

namespace Simply\Core\Contract;

/**
 * A Hookable is a class that add hooks into wordpress
 * The register function is called immediately in after_setup_theme hooks
 *
 * Don't use this interface if the hook you want to add is before after_setup_theme hook
 *
 * @package SimplyFramework\Contract
 */
interface HookableInterface
{
    /**
     * Register all hooks for the class
     * @return mixed
     */
    public function register();
}
