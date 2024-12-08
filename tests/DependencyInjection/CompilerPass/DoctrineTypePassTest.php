<?php

declare(strict_types=1);

namespace Npowest\Bundle\DoctrineTypes\Tests\DependencyInjection\CompilerPass;

use Npowest\Bundle\DoctrineTypes\DependencyInjection\CompilerPass\DoctrineTypePass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use RuntimeException;

final class DoctrineTypePassTest extends TestCase
{
    private DoctrineTypePass $doctrineTypePass;

    private ContainerBuilder $container;

    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir().'/doctrine_types_test';

        // Создаем временную структуру папок
        mkdir($this->tempDir, 0777, true);
        mkdir($this->tempDir.'/src', 0777, true);
        mkdir($this->tempDir.'/src/DBAL', 0777, true);
        mkdir($this->tempDir.'/src/DBAL/Types', 0777, true);

        // Создаем экземпляр класса DoctrineTypePass
        $this->doctrineTypePass = new DoctrineTypePass();
        $this->container = new ContainerBuilder();
        $this->container->setParameter(DoctrineTypePass::CONTAINER_TYPES_PARAMETER, []);
        
        // Устанавливаем каталог проекта
        $reflection = new ReflectionClass($this->doctrineTypePass);
        $projectDirProperty = $reflection->getProperty('projectDir');
        $projectDirProperty->setAccessible(true);
        $projectDirProperty->setValue($this->doctrineTypePass, $this->tempDir);
    }//end setUp()

    protected function tearDown(): void
    {
        // Удаляем временные файлы и директории если необходимо
        system("rm -rf {$this->tempDir}");
    }//end tearDown()

    public function testProcessAddsNewTypeToContainer(): void
    {
        // Создаём фиктивный класс с константой NAME
        $classCode = <<<'CODE'
        <?php

        namespace Npowest\Bundle\DoctrineTypes\DBAL\Types;

        class MockDbalType
        {
            public const NAME = 'mock_type';
        }
        CODE;

        // Записываем временный файл для теста
        $mockFilePath = $this->tempDir.'/src/DBAL/Types/MockDbalType.php';
        file_put_contents($mockFilePath, $classCode);
        // Явно подключаем класс для теста
        require_once $this->tempDir.'/src/DBAL/Types/MockDbalType.php';
 
        // Проверяем, что файл был создан
        $this->assertFileExists($mockFilePath);

        $this->doctrineTypePass->process($this->container);

        $expected = ['mock_type' => ['class' => 'Npowest\Bundle\DoctrineTypes\DBAL\Types\MockDbalType']];
        $this->assertSame($expected, $this->container->getParameter(DoctrineTypePass::CONTAINER_TYPES_PARAMETER));
    }//end testProcessAddsNewTypeToContainer()

    public function testProcessThrowsExceptionIfSrcFolderDoesNotExist(): void
    {
        // Изменяем проектный каталог на несуществующий
        $reflection = new ReflectionClass($this->doctrineTypePass);
        $projectDirProperty = $reflection->getProperty('projectDir');
        $projectDirProperty->setAccessible(true);
        $projectDirProperty->setValue($this->doctrineTypePass, '/path/to/nonexistent/directory');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The source folder "/path/to/nonexistent/directory/src" does not exist.');

        $this->doctrineTypePass->process($this->container);
    }//end testProcessThrowsExceptionIfSrcFolderDoesNotExist()

    public function testGenerateTypesFindsClasses(): void
    {
        // Создаём фиктивный класс с константой NAME
        $classCode = <<<'CODE'
        <?php

        namespace Npowest\Bundle\DoctrineTypes\DBAL\Types;

        class MockDbalType
        {
            public const NAME = 'mock_type';
        }
        CODE;

        // Записываем временный файл для теста
        $mockFilePath = $this->tempDir.'/src/DBAL/Types/MockDbalType.php';
        file_put_contents($mockFilePath, $classCode);
        $this->assertFileExists($mockFilePath);
        // Явно подключаем класс для теста
        require_once $this->tempDir.'/src/DBAL/Types/MockDbalType.php';
 
        // Теперь вызовем generateTypes непосредственно, чтобы проверить обнаружение
        $reflection = new ReflectionClass($this->doctrineTypePass);
        $method = $reflection->getMethod('generateTypes');
        $method->setAccessible(true);
        
        // Проверяем найденные классы
        $foundTypes = iterator_to_array($method->invoke($this->doctrineTypePass));

        $expected = [
            [
                'namespace' => 'Npowest\\Bundle\\DoctrineTypes\\DBAL\\Types\\MockDbalType',
                'name' => 'mock_type',
            ],
        ];

        $this->assertSame($expected, $foundTypes);
    }//end testGenerateTypesFindsClasses()
}//end class
