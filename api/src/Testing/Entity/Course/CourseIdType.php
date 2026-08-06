<?php

declare(strict_types=1);

namespace App\Testing\Entity\Course;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/** @psalm-suppress  UnusedClass */
final class CourseIdType extends StringType
{
    public const string NAME = 'course_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof CourseId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?CourseId
    {
        return !empty($value) ? new CourseId((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
