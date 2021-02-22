<?php

declare(strict_types=1);

namespace Andante\SoftDeletableBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
                ->scalarNode('delete_at_property_name')
                ->defaultValue(self::DEFAULT_DELETE_AT_PROPERTY_NAME)
                ->end()
            ->end();
        //@formatter:on

        return $treeBuilder;
    }
}
