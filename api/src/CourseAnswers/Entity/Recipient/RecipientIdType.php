<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Recipient;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class RecipientIdType extends StringType
{
    public const string NAME = 'recipient_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof RecipientId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?RecipientId
    {
        return !empty($value) ? new RecipientId((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
