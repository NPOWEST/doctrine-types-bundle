<?php

/**
 * @see https://npowest.ru
 *
 * @license Shareware
 * @copyright (c) 2019-2024 NPOWest
 */

declare(strict_types=1);

namespace Npowest\Bundle\DoctrineTypes\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class DoctrineTypesExtension extends Extension
{
    public function getAlias(): string
    {
        return 'npowest_doctrine_types';
    }//end getAlias()

    public function load(array $configs, ContainerBuilder $container): void {}//end load()
}//end class
