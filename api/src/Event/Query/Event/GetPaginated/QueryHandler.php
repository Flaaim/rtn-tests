<?php

declare(strict_types=1);

namespace App\Event\Query\Event\GetPaginated;

use App\Event\Query\Event\EventFetcherInterface;

final readonly class QueryHandler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private EventFetcherInterface $events
    ) {}

    public function handle(Query $query): ListEventDTO
    {
        $safeLimit = max(1, $query->limit);

        $result = $this->events->getPaginated(
            $query->page,
            $query->limit,
            $query->search,
        );

        $items = array_map(
            static fn (array $event): EventDTO => EventDTO::fromArray($event),
            $result['items']
        );

        $totalCount = $result['totalCount'];

        $totalPages = $totalCount > 0 ? (int)ceil($totalCount / $safeLimit) : 0;

        return new ListEventDTO(
            items: $items,
            totalCount: $totalCount,
            totalPages: $totalPages,
        );
    }
}
