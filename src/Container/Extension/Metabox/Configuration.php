<?php

namespace SimplyFramework\Container\Extension\Metabox;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder('metabox');
        $treeBuilder->getRootNode()->useAttributeAsKey('key')->arrayPrototype()
            ->children()
                ->variableNode('screen')->end()
                ->variableNode('title')->end()
                ->variableNode('callable')->end()
            ->end();
        return $treeBuilder;
    }
}
