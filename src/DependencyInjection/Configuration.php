<?php

namespace Npowest\Bundle\DoctrineTypes\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('npowest_doctrine_types');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('security')
                    ->children()
                        ->arrayNode('providers')
                            ->children()
                                ->arrayNode('entity')
                                    ->children()
                                        ->scalarNode('class')->defaultValue('App\Entity\User')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ;

        return $treeBuilder;
    }
}
