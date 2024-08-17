<?php

declare(strict_types=1);

namespace Npowest\Bundle\DoctrineTypes\Doctrine\DBAL\Types;

use Npowest\Bundle\DoctrineTypes\Contracts\DoctrineType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

abstract class AbstractFixedLengthStringType extends Type implements DoctrineType
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = $this->getLength();
        return $platform->getStringTypeDeclarationSQL($column);
    }//end getSQLDeclaration()

    abstract protected function getLength(): int;
}//end class
