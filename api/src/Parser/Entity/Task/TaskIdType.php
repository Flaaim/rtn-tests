<?php

declare(strict_types=1);

namespace App\Parser\Entity\Task;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class TaskIdType extends StringType
{
    public const string NAME = 'task_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof TaskId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?TaskId
    {
        return !empty($value) ? new TaskId((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
