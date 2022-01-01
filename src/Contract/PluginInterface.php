<?php

namespace Simply\Core\Contract;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Use this interface is like a symfony bundle, you can add compiler pass and other extension in build function
 */
interface PluginInterface {
    public function build(ContainerBuilder $container): void;
}
