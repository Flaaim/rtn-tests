<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Product\GetPaginated;

use App\CourseAnswers\Query\Product\ProductFetcherInterface;

final class QueryHandler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private ProductFetcherInterface $products,
    ) {}

    public function handle(Query $query): ListProductDTO
    {
        $safeLimit = max(1, $query->limit);

        $result = $this->products->getPaginated(
            $query->page,
            $query->limit,
            $query->search,
        );

        $items = array_map(
            static fn (array $product): ProductDTO => ProductDTO::fromArray($product),
            $result['items']
        );

        $totalCount = $result['totalCount'];

        $totalPages = $totalCount > 0 ? (int)ceil($totalCount / $safeLimit) : 0;

        return new ListProductDTO(
            items: $items,
            totalCount: $totalCount,
            totalPages: $totalPages,
        );
    }
}
