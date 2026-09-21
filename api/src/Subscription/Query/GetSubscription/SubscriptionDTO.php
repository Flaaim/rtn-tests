<?php

declare(strict_types=1);

namespace App\Subscription\Query\GetSubscription;

final readonly class SubscriptionDTO
{
    public function __construct(
        public bool $hasAccess,
        public string $plan,
        public string $status,
        public string $periodStart,
        public string $periodEnd,
        public bool $trialUsed,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            hasAccess: true,
            plan: $data['plan'],
            status: $data['status'],
            periodStart: $data['period_start'],
            periodEnd: $data['period_end'],
            trialUsed: $data['is_trial_used'],
        );
    }
}
