<?php

declare(strict_types=1);

namespace App\Subscription\Query\Subscription;

interface SubscriptionFetcherInterface
{
    public function getLatestByUserId(string $userId): array;

    public function getByUserPaginated(string $userId, int $page, int $limit): array;

    public function getPaginated(int $page, int $limit, ?string $search = null): array;
}
