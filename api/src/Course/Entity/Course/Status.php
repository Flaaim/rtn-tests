<?php

declare(strict_types=1);

namespace App\Course\Entity\Course;

use Webmozart\Assert\Assert;

final class Status
{
    public const string PROCESSING = 'processing';
    public const string CREATED = 'created';

    private string $value;

    public function __construct(string $value)
    {
        Assert::oneOf($value, [self::PROCESSING, self::CREATED]);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function processing(): self
    {
        return new self(self::PROCESSING);
    }

    public static function created(): self
    {
        return new self(self::CREATED);
    }

    public function isProcessing(): bool
    {
        return self::PROCESSING === $this->value;
    }

    public function isCreated(): bool
    {
        return self::CREATED === $this->value;
    }
}
