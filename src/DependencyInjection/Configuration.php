<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\DependencyInjection;

use Andante\SoftDeletableBundle\Config\EntityConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const DEFAULT_DELETE_AT_PROPERTY_NAME = 'deletedAt';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('andante_soft_deletable');

        //@formatter:off
        /** @var ArrayNodeDefinition $node */
        $node = $treeBuilder->getRootNode();
            $node->children()
                ->booleanNode('deleted_date_aware')
                    ->defaultTrue()
                ->end()
                ->arrayNode('default')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('property_name')
                            ->defaultValue(EntityConfiguration::DEFAULT_DELETED_AT_PROPERTY_NAME)
                            ->info('Entity property to be mapped as deletedAt property')
                        ->end()
                        ->scalarNode('column_name')
                            ->defaultNull()
                            ->info('Database column name to be used. Set to "null" to use default doctrine naming strategy')
                        ->end()
                        ->scalarNode('table_index')
                            ->defaultTrue()
                            ->info('If TRUE, adds a table index to the deletedAt property')
                        ->end()
                        ->scalarNode('always_update_deleted_at')
                            ->defaultTrue()
                            ->info('If true, deletedAt property will be always updated on delete, even if there is already a "deletedAt" date')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('entity')
                    ->arrayPrototype()
                    ->children()
                        ->scalarNode('entity')
                        ->end()
                        ->scalarNode('property_name')
                            ->defaultValue(EntityConfiguration::DEFAULT_DELETED_AT_PROPERTY_NAME)
                            ->info('Entity property to be mapped as deletedAt property')
                        ->end()
                        ->scalarNode('column_name')
                            ->info('Database column name to be used. Set to "null" to use default doctrine naming strategy')
                        ->end()
                        ->scalarNode('table_index')
                            ->info('If TRUE, adds a table index to the deletedAt property')
                        ->end()
                        ->scalarNode('always_update_deleted_at')
                            ->info('If true, deletedAt property will be always updated on delete, even if there is already a "deletedAt" date')
                        ->end()
                    ->end()
                ->end()
            ->end();
        //@formatter:on

        return $treeBuilder;
    }

    private function addEntityConfigurationNode(string $rootName): NodeDefinition
    {
        $node = new ArrayNodeDefinition($rootName);
        //@formatter:off
        $node
            ->useAttributeAsKey('entity')
            ->addDefaultsIfNotSet()
            ->arrayPrototype()
            ->children()
                ->scalarNode('entity')
                    ->defaultValue('default')
                ->end()
                ->scalarNode('property_name')
                    ->defaultValue(EntityConfiguration::DEFAULT_DELETED_AT_PROPERTY_NAME)
                    ->info('Entity property to be mapped as deletedAt property')
                ->end()
                ->scalarNode('column_name')
                    ->defaultNull()
                    ->info('Database column name to be used. Set to "null" to use default doctrine naming strategy')
                ->end()
                ->scalarNode('table_index')
                    ->defaultTrue()
                    ->info('If TRUE, adds a table index to the deletedAt property')
                ->end()
                ->scalarNode('always_update_deleted_at')
                    ->defaultTrue()
                    ->info('If true, deletedAt property will be always updated on delete, even if there is already a "deletedAt" date')
                ->end()
            ->end();
        //@formatter:on
        return $node;
    }
}
