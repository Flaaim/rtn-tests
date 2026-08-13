<?php

declare(strict_types=1);

namespace App\Testing\Entity\Test;

use Webmozart\Assert\Assert;

final class Status
{
    public const string  ACTIVE = 'active';
    public const string  INACTIVE = 'inactive';

    public function __construct(
        private string $value
    ) {
        Assert::oneOf($value, [self::ACTIVE, self::INACTIVE]);
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

    public function isActive(): bool
    {
        return self::ACTIVE === $this->value;
    }

    public function isInactive(): bool
    {
        return self::INACTIVE === $this->value;
    }
}
