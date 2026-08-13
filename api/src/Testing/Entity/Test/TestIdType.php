<?php

declare(strict_types=1);

namespace App\Testing\Entity\Test;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/** @psalm-suppress  UnusedClass */
final class TestIdType extends StringType
{
    public const string NAME = 'test_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof TestId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?TestId
    {
        return !empty($value) ? new TestId((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
