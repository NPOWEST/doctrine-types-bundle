<?php

/**
 * @see https://npowest.ru
 *
 * @license Shareware
 * @copyright (c) 2019-2024 NPOWest
 */

declare(strict_types=1);

namespace Npowest\Bundle\DoctrineTypes\DBAL\AbstractTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Npowest\Bundle\DoctrineTypes\Contracts\DoctrineType;

abstract class AbstractFixedJsonType extends Type implements DoctrineType
{
    final public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }//end getSQLDeclaration()
}//end class
