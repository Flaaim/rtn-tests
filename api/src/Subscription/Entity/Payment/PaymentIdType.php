<?php

declare(strict_types=1);

namespace App\Subscription\Entity\Payment;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/** @psalm-suppress UnusedClass */
final class PaymentIdType extends StringType
{
    public const string NAME = 'payment_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof PaymentId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?PaymentId
    {
        return !empty($value) ? new PaymentId((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
