<?php

namespace Simply\Core\DependencyInjection\Extension\Taxonomy;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder('taxonomy');
        $treeBuilder->getRootNode()->useAttributeAsKey('key')->arrayPrototype()
            ->children()
                ->variableNode('object_type')->isRequired()->end()
                ->variableNode('args')->end()
            ->end();
        return $treeBuilder;
    }
}
