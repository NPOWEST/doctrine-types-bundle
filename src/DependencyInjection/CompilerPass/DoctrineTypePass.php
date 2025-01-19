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
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function array_key_exists;
use function is_string;
use function sprintf;
use function str_contains;
use function str_replace;

final class DoctrineTypePass implements CompilerPassInterface
{
    public const CONTAINER_TYPES_PARAMETER = 'doctrine.dbal.connection_factory.types';

    private const TYPE_NAME_CONSTANT_NAME = 'NAME';

    private const SRC_FOLDER_MASK = '%s/src';

    private string $projectDir;

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

            if (! array_key_exists($name, $typeDefinition))
            {
                $typeDefinition[$name] = ['class' => $namespace];
            }
        }

        $container->setParameter(self::CONTAINER_TYPES_PARAMETER, $typeDefinition);
    }//end process()

    /**
     * @phpstan-ignore missingType.generics
     */
    private function isSupportedType(ReflectionClass $reflection): bool
    {
        return $reflection->hasConstant(self::TYPE_NAME_CONSTANT_NAME)
            && is_string($reflection->getConstant(self::TYPE_NAME_CONSTANT_NAME));
    }//end isSupportedType()

    /**
     * @return Generator<int, array{namespace: class-string, name: string}>
     *
     * @throws RuntimeException
     */
    private function generateTypes(): iterable
    {
        $srcFolder = sprintf(self::SRC_FOLDER_MASK, $this->projectDir);

        if (! is_dir($srcFolder))
        {
            throw new RuntimeException(sprintf('The source folder "%s" does not exist.', $srcFolder));
        }

        // Используем glob для поиска всех PHP файлов в директории
        $files = $this->getFiles($srcFolder);

        foreach ($files as $file)
        {
            $namespace = $this->getNamespaceFromFile($file);

            if (! str_contains($namespace, 'DBAL\\Types'))
            {
                continue;
            }

            try
            {
                $reflection = new ReflectionClass($namespace);

                if ($this->isSupportedType($reflection))
                {
                    yield [
                        'namespace' => $reflection->getName(),
                        'name'      => $reflection->getConstant(self::TYPE_NAME_CONSTANT_NAME),
                    ];
                }
            }
            catch (ReflectionException $e)
            {
                error_log($e->getMessage());
            }
        }//end foreach
    }//end generateTypes()

    private function getNamespaceFromFile(string $file): string
    {
        $filePart = explode('src/', $file);
        $file     = end($filePart);
        // Определяем пространство имен, заменяя пути на обратные слеши и убирая .php
        $relativePath = str_replace([$this->projectDir.'/src/', '.php'], ['', ''], $file);
        $namespace    = str_replace('/', '\\', $relativePath);

        return 'Npowest\\Bundle\\DoctrineTypes\\'.mb_ltrim($namespace, '\\');
    }//end getNamespaceFromFile()

    /**
     * @return string[]
     */
    private function getFiles(string $path): array
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $phpFiles = [];

        foreach ($iterator as $fileInfo)
        {
            if ($fileInfo->isFile() && 'php' === $fileInfo->getExtension())
            {
                $phpFiles[] = $fileInfo->getRealPath();
            }
        }

        return $phpFiles;
    }//end getFiles()
}//end class
