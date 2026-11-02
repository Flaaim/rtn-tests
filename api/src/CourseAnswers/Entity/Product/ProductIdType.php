<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Product;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/** @psalm-suppress UnusedClass */
final class ProductIdType extends StringType
{
    public const string NAME = 'product_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof ProductId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ProductId
    {
        return !empty($value) ? new ProductId((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
