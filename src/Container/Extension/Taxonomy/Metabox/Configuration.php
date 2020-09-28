<?php

namespace SimplyFramework\Container\Extension\Taxonomy\Metabox;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder('term_metabox');
        $treeBuilder->getRootNode()->useAttributeAsKey('key')->arrayPrototype()
            ->children()
                ->variableNode('taxonomy')->isRequired()->end()
                ->variableNode('name')->isRequired()->end()
                ->variableNode('fields')->isRequired()->end()
            ->end();
        return $treeBuilder;
    }
}
