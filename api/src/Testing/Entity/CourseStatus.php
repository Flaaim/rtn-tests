<?php

declare(strict_types=1);

namespace App\Testing\Entity;

use Webmozart\Assert\Assert;

final class CourseStatus
{
    public const string ACTIVE = 'active';
    public const string INACTIVE = 'inactive';
    public const string PROCESSING = 'processing';

    private string $value;

    public function __construct(string $value)
    {
        Assert::oneOf($value, [self::ACTIVE, self::INACTIVE, self::PROCESSING]);
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

    public function processing(): self
    {
        return new self(self::PROCESSING);
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
}
