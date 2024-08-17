<?php

/**
 * @see https://npowest.ru
 *
 * @license Shareware
 * @copyright (c) 2019-2024 NPOWest
 */

declare(strict_types=1);

namespace Npowest\Bundle\DoctrineTypes\DependencyInjection\CompilerPass;

use Generator;
use League\ConstructFinder\ConstructFinder;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function array_key_exists;
use function is_string;

final class DoctrineTypePass implements CompilerPassInterface
{
    private const CONTAINER_TYPES_PARAMETER = 'doctrine.dbal.connection_factory.types';

    private const PROJECT_TYPES_PATTERN = '/DBAL\\\\Types(\\\\(.*))?/i';

    private const TYPE_NAME_CONSTANT_NAME = 'NAME';

    private const SRC_FOLDER_MASK = '%s/src';

    private string $projectDir = '';

    public function __construct()
    {
        $this->projectDir = __DIR__.'/../../..';
    }//end __construct()

    public function process(ContainerBuilder $container): void
    {
        /** @var array<string, array{class: class-string}> $typeDefinition */
        $typeDefinition = $container->getParameter(self::CONTAINER_TYPES_PARAMETER);

        $types = $this->generateTypes();

        /** @var array{namespace: string, name: string} $type */
        foreach ($types as $type)
        {
            $name      = $type['name'];
            $namespace = $type['namespace'];

            if (array_key_exists($name, $typeDefinition))
            {
                continue;
            }

            $typeDefinition[$name] = ['class' => $namespace];
        }

        $container->setParameter(self::CONTAINER_TYPES_PARAMETER, $typeDefinition);
    }//end process()

    /**
     * @return Generator<int, array{namespace: class-string, name: string}>
     */
    private function generateTypes(): iterable
    {
        $srcFolder = sprintf(self::SRC_FOLDER_MASK, $this->projectDir);

        $classNames = ConstructFinder::locatedIn($srcFolder)->findClassNames();

        foreach ($classNames as $className)
        {
            if (0 === preg_match(self::PROJECT_TYPES_PATTERN, $className))
            {
                continue;
            }

            $reflection = new ReflectionClass($className);

            if (! $reflection->hasConstant(self::TYPE_NAME_CONSTANT_NAME))
            {
                continue;
            }

            $constantValue = $reflection->getConstant(self::TYPE_NAME_CONSTANT_NAME);

            if (! is_string($constantValue))
            {
                continue;
            }

            yield [
                'namespace' => $reflection->getName(),
                'name'      => $constantValue,
            ];
        }//end foreach
    }//end generateTypes()
}//end class
