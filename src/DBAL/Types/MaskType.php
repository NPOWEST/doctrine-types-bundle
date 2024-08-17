<?php

declare(strict_types=1);

namespace Npowest\Bundle\DoctrineTypes\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Npowest\GardenHelper\Mask;

use function assert;
use function is_resource;
use function is_string;

final class MaskType extends AbstractFixedLengthStringType
{
    public const NAME   = 'device_mask';
    public const LENGTH = 5;

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }//end getSQLDeclaration()

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        assert($value instanceof Mask);

        return $value->get();
    }//end convertToDatabaseValue()

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Mask
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        assert(is_string($value));

        return new Mask($value);
    }//end convertToPHPValue()

    public function getName(): string
    {
        return self::NAME;
    }//end getName()

    protected function getLength(): int
    {
        return self::LENGTH;
    }//end getLength()
}//end class
