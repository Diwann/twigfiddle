<?php

namespace Fuz\Process\ConfigurationNode;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Fuz\Framework\Api\ConfigurationNodeInterface;

class EnvironmentConfigurationNode implements ConfigurationNodeInterface
{

    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('environment');

        $rootNode
           ->children()
                ->scalarNode("directory")
                    ->isRequired()
                ->end()
                ->scalarNode("validation")
                    ->defaultValue('^[a-zA-Z0-9-]{4,16}$')
                ->end()
                ->integerNode('expiry')
                    ->defaultValue(24)
                ->end()
           ->end()
        ;

        return $rootNode;
    }

}
