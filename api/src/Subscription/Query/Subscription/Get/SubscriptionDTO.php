<?php

declare(strict_types=1);

namespace App\Subscription\Query\Subscription\Get;

final readonly class SubscriptionDTO
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $plan,
        public string $status,
        public string $periodStart,
        public string $periodEnd,
        public int $durationDays,
        public string $email
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            userId: $data['user_id'],
            plan: $data['plan'],
            status: $data['status'],
            periodStart: $data['period_start'],
            periodEnd: $data['period_end'],
            durationDays: $data['duration_days'],
            email: $data['email'],
        );
    }
}
