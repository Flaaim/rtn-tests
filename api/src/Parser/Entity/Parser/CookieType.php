<?php

declare(strict_types=1);

namespace App\Parser\Entity\Parser;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class CookieType extends StringType
{
    public const string NAME = 'cookie';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof Cookie ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Cookie
    {
        return !empty($value) ? new Cookie($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
