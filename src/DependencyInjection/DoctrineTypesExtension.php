<?php

namespace Npowest\Bundle\DoctrineTypes\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class DoctrineTypesExtension extends Extension
{
    public function getAlias(): string
    {
        return 'npowest_doctrine_types';
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.php');

        $container->setParameter('npowest_doctrine_types.security.providers.entity.class', $config['security']['providers']['entity']['class']);
    }
}
