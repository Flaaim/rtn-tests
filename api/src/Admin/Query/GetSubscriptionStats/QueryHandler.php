<?php

declare(strict_types=1);

namespace App\Admin\Query\GetSubscriptionStats;

use App\Admin\Query\AdminFetcherInterface;

final readonly class QueryHandler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private AdminFetcherInterface $fetcher,
    ) {}

    public function handle(): SubscriptionStatsDTO
    {
        $result = $this->fetcher->getSubscriptionStats();

        return SubscriptionStatsDTO::fromArray($result);
    }
}
