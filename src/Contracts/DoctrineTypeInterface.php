<?php

/**
 * @see https://npowest.ru
 *
 * @license Shareware
 * @copyright (c) 2019-2024 NPOWest
 */

declare(strict_types=1);

namespace Npowest\Bundle\DoctrineTypes\Contracts;

use Doctrine\DBAL\Platforms\AbstractPlatform;

interface DoctrineTypeInterface
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string;
}//end interface
