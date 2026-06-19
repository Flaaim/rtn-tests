<?php

declare(strict_types=1);

namespace App\Parser\Entity\Parser;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class ParserIdType extends StringType
{
    public const string NAME = 'parser_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof ParserId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ParserId
    {
        return !empty($value) ? new ParserId((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
