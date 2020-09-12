<?php

namespace SimplyFramework\Container\Extension\PostType;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder('post_type');
        $treeBuilder->getRootNode()->useAttributeAsKey('key')->arrayPrototype()
            ->children()
                ->booleanNode('public')->defaultTrue()->end()
                ->booleanNode('hierarchical')->defaultFalse()->end()
                ->variableNode('label')->end()
            ->end();
        return $treeBuilder;
    }
}
