<?php

declare(strict_types=1);

namespace App\Admin\Stats\Query\Subscriptions;

use App\Admin\Stats\Query\StatsFetcherInterface;
use DomainException;

final readonly class QueryHandler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private StatsFetcherInterface $fetcher
    ) {}

    public function handle(): SubscriptionsStatsDTO
    {
        $result = $this->fetcher->getSubscriptionsStats();
        if (null === $result) {
            throw new DomainException('Can not fetch subsription statistics.');
        }
        return SubscriptionsStatsDTO::fromArray($result);
    }
}
