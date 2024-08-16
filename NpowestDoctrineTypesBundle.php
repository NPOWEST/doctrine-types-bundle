<?php

namespace Npowest\Bundle\DoctrineTypes;

use Npowest\Bundle\DoctrineTypes\DependencyInjection\CompilerPass\DoctrineTypePass;
use Npowest\Bundle\DoctrineTypes\DependencyInjection\DoctrineTypesExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NpowestDoctrineTypesBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new DoctrineTypesExtension();
        }
        return $this->extension;
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new DoctrineTypePass());
    }
}
