<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Recipient\GetPaginated;

use App\CourseAnswers\Query\Recipient\RecipientFetcherInterface;

final readonly class QueryHandler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private RecipientFetcherInterface $recipients
    ) {}

    public function handle(Query $query): ListRecipientDTO
    {
        $safeLimit = max(1, $query->limit);

        $result = $this->recipients->getPaginated(
            $query->page,
            $query->limit,
            $query->search,
        );

        $items = array_map(
            static fn (array $product): RecipientDTO => RecipientDTO::fromArray($product),
            $result['items']
        );

        $totalCount = $result['totalCount'];

        $totalPages = $totalCount > 0 ? (int)ceil($totalCount / $safeLimit) : 0;

        return new ListRecipientDTO(
            items: $items,
            totalCount: $totalCount,
            totalPages: $totalPages,
        );
    }
}
