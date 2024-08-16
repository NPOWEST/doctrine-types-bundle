<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Npowest\Bundle\DoctrineTypes\Contracts\DoctrineType;
use Npowest\Bundle\DoctrineTypes\DependencyInjection\CompilerPass\DoctrineTypePass;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->defaults()
        ->instanceof(DoctrineType::class)->tag(DoctrineTypePass::TAG);
};
