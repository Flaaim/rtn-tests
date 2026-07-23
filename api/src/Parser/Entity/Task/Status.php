<?php

declare(strict_types=1);

namespace App\Parser\Entity\Task;

use Webmozart\Assert\Assert;

final class Status
{
    public const string PROCESSING = 'processing';
    public const string COMPLETED = 'completed';
    public const string FAILED = 'failed';

    public function __construct(
        private string $value
    ) {
        Assert::oneOf($value, [self::PROCESSING, self::COMPLETED, self::FAILED]);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function processing(): self
    {
        return new self('processing');
    }

    public static function completed(): self
    {
        return new self('completed');
    }

    public static function failed(): self
    {
        return new self('failed');
    }

    public function isEqual(self $status): bool
    {
        return $this->value === $status->getValue();
    }
}
