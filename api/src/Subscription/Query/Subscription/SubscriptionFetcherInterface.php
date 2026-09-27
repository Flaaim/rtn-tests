<?php

declare(strict_types=1);

namespace App\Subscription\Query\Subscription;

interface SubscriptionFetcherInterface
{
    public function getByUserId(string $userId): array;

    public function hasActiveByUserId(string $userId): bool;

    public function isTrialUsedByUserId(string $userId): bool;

    public function getByUserPaginated(string $userId, int $page, int $limit): array;
}
