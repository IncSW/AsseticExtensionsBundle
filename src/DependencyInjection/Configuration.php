<?php

declare(strict_types = 1);

namespace IncSW\AsseticExtensionsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
// Interfaces
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package IncSW\AsseticExtensionsBundle\DependencyInjection
 */
final class Configuration implements ConfigurationInterface
{

    /**
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('incsw_assetic_extensions');
        $rootNode->append($this->getFiltersNode());

        return $treeBuilder;
    }

    /**
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    private function getFiltersNode()
    {
        $treeBuilder = new TreeBuilder();
        $filtersNode = $treeBuilder->root('filters');
        $filtersNode->append($this->getFiltersBabelNode());

        return $filtersNode;
    }

    /**
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    private function getFiltersBabelNode()
    {
        $treeBuilder = new TreeBuilder();
        $babelNode = $treeBuilder->root('babel');
        $babelNode
            ->children()
                ->scalarNode('bin')->end()
                ->scalarNode('config')->end()
            ->end()
        ;

        return $babelNode;
    }
}
