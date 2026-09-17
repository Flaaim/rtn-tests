<?php

declare(strict_types=1);

namespace App\Subscription\Command\CreatePayment;

final readonly class Command
{
    public function __construct(
        public string $plan,
        public string $amount,
        public int $durationDays,
        public string $userId,
        public string $returnUrl,
    ) {}
}
