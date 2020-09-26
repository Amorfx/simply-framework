<?php

namespace SimplyFramework\Container\Extension\Field;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder('field');
        $treeBuilder->getRootNode()->useAttributeAsKey('key')->arrayPrototype()
            ->children()
                ->variableNode('type')->end()
                ->variableNode('options')->end()
            ->end();
        return $treeBuilder;
    }
}
