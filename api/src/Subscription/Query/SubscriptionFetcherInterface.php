<?php

declare(strict_types=1);

namespace App\Subscription\Query;

interface SubscriptionFetcherInterface
{
    public function findByUserId(string $userId): array;

    public function hasActiveByUserId(string $userId): bool;

    public function hasTrialByUserId(string $userId): bool;
}
