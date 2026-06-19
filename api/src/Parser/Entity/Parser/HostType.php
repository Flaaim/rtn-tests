<?php

declare(strict_types=1);

namespace App\Parser\Entity\Parser;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class HostType extends StringType
{
    public const string NAME = 'parser_host';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof Host ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Host
    {
        return !empty($value) ? new Host((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
