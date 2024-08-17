<?php

declare(strict_types=1);

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
    }//end getAlias()

    public function load(array $configs, ContainerBuilder $container): void
    {
        // $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        // $loader->load('services.php');
    }//end load()
}//end class
