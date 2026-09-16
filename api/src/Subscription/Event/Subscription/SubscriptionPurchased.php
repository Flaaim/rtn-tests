<?php

declare(strict_types=1);

namespace App\Subscription\Event\Subscription;

final readonly class SubscriptionPurchased
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $plan,
        public string $ended
    ) {}
}
