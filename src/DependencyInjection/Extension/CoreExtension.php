<?php

namespace Simply\Core\DependencyInjection\Extension;

use Simply\Core\Contract\HookableInterface;
use Simply\Core\DependencyInjection\Compiler\HookPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class CoreExtension extends Extension {
    public function load(array $configs, ContainerBuilder $container) {
    }
}
