<?php

namespace Npowest\Bundle\DoctrineTypes\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineTypePass implements CompilerPassInterface
{
    public const TAG = 'npowest.doctrine_type';

    public function process(ContainerBuilder $container): void
    {
        $typesDefinition = [];
        if ($container->hasParameter('doctrine.dbal.connection_factory.types')) {
            $typesDefinition = $container->getParameter('doctrine.dbal.connection_factory.types');
        }
        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        foreach ($taggedServices as $customType => $definition) {
            $typesDefinition[$customType::NAME] = ['class' => $customType];
        }

        $container->setParameter('doctrine.dbal.connection_factory.types', $typesDefinition);
    }
}
