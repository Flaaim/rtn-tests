<?php

declare(strict_types=1);

namespace App\Subscription\Entity\Subscription;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/** @psalm-suppress UnusedClass */
final class SubscriptionIdType extends StringType
{
    public const string NAME = 'subscription_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof SubscriptionId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?SubscriptionId
    {
        return !empty($value) ? new SubscriptionId((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
