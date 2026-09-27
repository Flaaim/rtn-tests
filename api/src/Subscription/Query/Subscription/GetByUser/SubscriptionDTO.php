<?php

declare(strict_types=1);

namespace App\Subscription\Query\Subscription\GetByUser;

final class SubscriptionDTO
{
    public function __construct(
        public string $id,
        public string $plan,
        public int $durationDays,
        public string $periodStart,
        public string $periodEnd,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            plan: $data['plan'],
            durationDays: $data['duration_days'],
            periodStart: $data['period_start'],
            periodEnd: $data['period_end'],
        );
    }
}
