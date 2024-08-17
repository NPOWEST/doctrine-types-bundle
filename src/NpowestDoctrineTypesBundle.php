<?php

/**
 * @see https://npowest.ru
 *
 * @license Shareware
 * @copyright (c) 2019-2024 NPOWest
 */

declare(strict_types=1);

namespace Npowest\Bundle\DoctrineTypes;

use Npowest\Bundle\DoctrineTypes\DependencyInjection\CompilerPass\DoctrineTypePass;
use Npowest\Bundle\DoctrineTypes\DependencyInjection\DoctrineTypesExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NpowestDoctrineTypesBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension)
        {
            $this->extension = new DoctrineTypesExtension();
        }

        return $this->extension;
    }//end getContainerExtension()

    public function build(ContainerBuilder $container): void
    {
        $doctrineTypePass = new DoctrineTypePass();
        $container->addCompilerPass($doctrineTypePass);
    }//end build()
}//end class
