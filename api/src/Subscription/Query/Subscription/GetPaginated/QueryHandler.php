<?php

declare(strict_types=1);

namespace App\Subscription\Query\Subscription\GetPaginated;

use App\Subscription\Query\Subscription\SubscriptionFetcherInterface;

final readonly class QueryHandler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private SubscriptionFetcherInterface $subscriptions
    ) {}

    public function handle(Query $query): ListSubscriptionDTO
    {
        $safeLimit = max(1, $query->limit);

        $result = $this->subscriptions->getPaginated($query->page, $query->limit, $query->search);

        $items = array_map(
            static fn (array $row) => SubscriptionDTO::fromArray($row),
            $result['items']
        );

        $totalCount = $result['totalCount'];

        $totalPages = $totalCount > 0 ? (int)ceil($totalCount / $safeLimit) : 0;

        return new ListSubscriptionDTO(
            items: $items,
            totalCount: $totalCount,
            totalPages: $totalPages,
        );
    }
}
