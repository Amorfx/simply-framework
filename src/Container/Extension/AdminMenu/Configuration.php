<?php

namespace SimplyFramework\Container\Extension\AdminMenu;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder('admin_menu');
        $treeBuilder->getRootNode()->useAttributeAsKey('key')->arrayPrototype()
            ->children()
                ->variableNode('page_title')->isRequired()->end()
                ->variableNode('menu_title')->isRequired()->end()
                ->variableNode('capability')->isRequired()->end()
                ->variableNode('menu_slug')->isRequired()->end()
                ->variableNode('is_submenu')->defaultFalse()->end()
                ->variableNode('parent_slug')->end()
                ->variableNode('callable')->end()
                ->variableNode('icon_url')->end()
                ->variableNode('position')->end()
                ->variableNode('fields')->isRequired()->end()
            ->end();
        return $treeBuilder;
    }
}
