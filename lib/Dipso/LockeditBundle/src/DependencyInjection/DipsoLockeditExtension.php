<?php                                      
                                                     
namespace Dipso\LockeditBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DipsoLockeditExtension extends Extension
{

    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs,$container);
        $config = $this->processConfiguration($configuration,$configs);

        $definition = $container->getDefinition('dipso_lockedit.lockedit');

        $definition->setArgument(1,$config['ttl']);

    }
}
