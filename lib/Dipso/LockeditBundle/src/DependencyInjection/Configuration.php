<?php                                      
                                                     
namespace Dipso\LockeditBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): treeBuilder
    {
        $treeBuilder = new TreeBuilder('dipso_lockedit');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->integerNode('ttl')->defaultValue('600')->info('time in seconds before releasing the lock')->end()
            ->end();

        return $treeBuilder;
    }
}
