<?php

namespace Simply\Core\DependencyInjection\Extension\NavMenu;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('nav_menu');
        $treeBuilder->getRootNode()->useAttributeAsKey('key')->variablePrototype();
        return $treeBuilder;
    }
}
