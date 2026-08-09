<?php

declare(strict_types=1);

namespace App\Testing\Entity\Course;

use Webmozart\Assert\Assert;

final class Type
{
    public const string SINGLE_CHOICE = 'single_choice';
    public const string MULTIPLE_CHOICE = 'multiple_choice';
    public const string MATCHING = 'matching';
    public const string SEQUENCE = 'sequence';

    public function __construct(
        private string $value
    ) {
        Assert::oneOf($value, [self::SINGLE_CHOICE, self::MULTIPLE_CHOICE, self::MATCHING, self::SEQUENCE]);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function singleChoice(): self
    {
        return new self(self::SINGLE_CHOICE);
    }

    public static function multipleChoice(): self
    {
        return new self(self::MULTIPLE_CHOICE);
    }

    public static function matching(): self
    {
        return new self(self::MATCHING);
    }

    public static function sequence(): self
    {
        return new self(self::SEQUENCE);
    }

    public function isSingleChoice(): bool
    {
        return self::SINGLE_CHOICE === $this->value;
    }

    public function isMultipleChoice(): bool
    {
        return self::MULTIPLE_CHOICE === $this->value;
    }

    public function isMatching(): bool
    {
        return self::MATCHING === $this->value;
    }

    public function isSequence(): bool
    {
        return self::SEQUENCE === $this->value;
    }
}
