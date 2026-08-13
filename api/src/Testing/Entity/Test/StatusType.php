<?php

declare(strict_types=1);

namespace App\Testing\Entity\Test;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class StatusType extends StringType
{
    public const string NAME = 'test_status';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof Status ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Status
    {
        return !empty($value) ? new Status((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
