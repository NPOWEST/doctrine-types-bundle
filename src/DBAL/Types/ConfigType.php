<?php

declare(strict_types=1);

namespace Npowest\Bundle\DoctrineTypes\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\{ConversionException, Type, Types};
use JsonException;
use Npowest\GardenHelper\Collection\ConfigCollection;

use function assert;
use function is_array;
use function is_resource;

use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_THROW_ON_ERROR;

final class ConfigType extends AbstractFixedJsonType
{
    public const NAME = 'device_config';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        assert($value instanceof ConfigCollection);
        $value = $value->toArray();

        try {
            return json_encode($value, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION);
        } catch (JsonException $e) {
            throw ConversionException::conversionFailedSerialization($value, 'json', $e->getMessage());
        }
    }//end convertToDatabaseValue()

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ConfigCollection
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        try {
            /** @var array<mixed>|false */
            $array = json_decode((string) $value, true, 512, JSON_THROW_ON_ERROR);
            $value = new ConfigCollection();
            if (is_array($array)) {
                $value->setFromArray($array);
            }

            return $value;
        } catch (JsonException $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }
    }//end convertToPHPValue()

    public function getName(): string
    {
        return self::NAME;
    }//end getName()
}//end class
