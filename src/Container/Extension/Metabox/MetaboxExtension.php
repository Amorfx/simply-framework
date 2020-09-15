<?php

namespace SimplyFramework\Container\Extension\Metabox;


use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class MetaboxExtension implements ExtensionInterface {
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);
        $container->setParameter('metaboxes', $config);
    }

    public function getNamespace() {
        return false;
    }

    public function getXsdValidationBasePath() {}

    public function getAlias() {
        return 'metabox';
    }
}
