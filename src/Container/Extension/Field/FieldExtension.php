<?php

namespace SimplyFramework\Container\Extension\Field;


use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class FieldExtension implements ExtensionInterface {
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);
        $container->setParameter('fields', $config);
    }

    public function getNamespace() {
        return false;
    }

    public function getXsdValidationBasePath() {}

    public function getAlias() {
        return 'field';
    }
}
