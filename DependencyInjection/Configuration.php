<?php

namespace phpFastCache\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package phpFastCache\Bundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws \RuntimeException
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('php_fast_cache');

        $rootNode
            ->children()
                ->arrayNode('drivers')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->enumNode('type')->isRequired()->values(array('Files', 'MongoDb', 'Apc'))->end() // @TODO : Add all available drivers
                            ->arrayNode('parameters')->isRequired()->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end() // drivers
            ->end();

        return $treeBuilder;
    }
}