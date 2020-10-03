<?php

namespace SimplyFramework\Container\Extension\AdminMenu;


use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class AdminMenuExtension implements ExtensionInterface {
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);
        $container->setParameter('admin_menus', $config);
    }

    public function getNamespace() {
        return false;
    }

    public function getXsdValidationBasePath() {}

    public function getAlias() {
        return 'admin_menu';
    }
}
