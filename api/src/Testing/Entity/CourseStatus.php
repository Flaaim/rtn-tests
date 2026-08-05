<?php

declare(strict_types=1);

namespace App\Testing\Entity;

use Webmozart\Assert\Assert;

final class CourseStatus
{
    public const string ACTIVE = 'active';
    public const string INACTIVE = 'inactive';
    public const string PROCESSING = 'processing';
    public const string CREATED = 'created';

    private string $value;

    public function __construct(string $value)
    {
        Assert::oneOf($value, [self::ACTIVE, self::INACTIVE, self::PROCESSING, self::CREATED]);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public static function inactive(): self
    {
        return new self(self::INACTIVE);
    }

    public static function processing(): self
    {
        return new self(self::PROCESSING);
    }

    public static function created(): self
    {
        return new self(self::CREATED);
    }

    public function isActive(): bool
    {
        return self::ACTIVE === $this->value;
    }

    public function isInactive(): bool
    {
        return self::INACTIVE === $this->value;
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
