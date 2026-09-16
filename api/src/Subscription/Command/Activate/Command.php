<?php

declare(strict_types=1);

namespace App\Subscription\Command\Activate;

final readonly class Command
{
    public function __construct(
        public string $userId,
        public int $durationDays,
        public string $plan,
    ) {}
}
