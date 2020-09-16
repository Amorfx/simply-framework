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
                ->variableNode('label')->end()
                ->variableNode('labels')->end()
                ->variableNode('description')->end()
                ->booleanNode('public')->defaultFalse()->end()
                ->variableNode('description')->end()
                ->booleanNode('hierarchical')->defaultFalse()->end()
                ->booleanNode('exclude_from_search')->end()
                ->variableNode('publicly_queryable')->end()
                ->booleanNode('show_ui')->end()
                ->variableNode('show_in_menu')->end()
                ->booleanNode('show_in_nav_menus')->end()
                ->booleanNode('show_in_admin_bar')->end()
                ->booleanNode('show_in_rest')->end()
                ->variableNode('rest_base')->end()
                ->variableNode('rest_controller_class')->end()
                ->integerNode('menu_position')->end()
                ->variableNode('menu_icon')->end()
                ->variableNode('capability_type')->end()
                ->variableNode('capabilities')->end()
                ->booleanNode('map_meta_cap')->end()
                ->variableNode('supports')->end()
                ->variableNode('register_meta_box_cb')->end()
                ->variableNode('taxonomies')->end()
                ->variableNode('has_archive')->end()
                ->arrayNode('rewrite')
                    ->children()
                        ->variableNode('slug')->end()
                        ->variableNode('with_front')->end()
                        ->variableNode('feeds')->end()
                        ->booleanNode('pages')->end()
                        ->variableNode('ep_mask')->end()
                    ->end()
                ->end()
                ->variableNode('query_var')->end()
                ->booleanNode('can_export')->defaultTrue()->end()
                ->booleanNode('delete_with_user')->end()
            ->end();
        return $treeBuilder;
    }
}
