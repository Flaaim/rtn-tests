<?php

declare(strict_types=1);

namespace App\Testing\Entity\Course;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/** @psalm-suppress  UnusedClass */
final class QuestionFormType extends StringType
{
    public const string NAME = 'question_form';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof QuestionForm ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?QuestionForm
    {
        return empty($value) ? new QuestionForm((string)$value) : null;
    }

    public function getName(): string
    {
        return 'question_type';
    }
}
