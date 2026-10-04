<?php

declare(strict_types=1);

namespace App\Admin\Stats\Query\Subscriptions;

final readonly class SubscriptionsStatsDTO
{
    public function __construct(
        public int $trialSubscriptions,
        public int $activeSubscriptions,
        public int $expiredSubscriptions,
        public int $waitSubscriptions,
        public float $conversionRate,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            trialSubscriptions: $data['trial_subscriptions'],
            activeSubscriptions: $data['active_subscriptions'],
            expiredSubscriptions: $data['expired_subscriptions'],
            waitSubscriptions: $data['wait_subscriptions'],
            conversionRate: (float)$data['conversion_rate'],
        );
    }
}
