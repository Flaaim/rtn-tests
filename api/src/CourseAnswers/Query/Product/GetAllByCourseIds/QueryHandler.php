<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Product\GetAllByCourseIds;

use App\CourseAnswers\Query\Product\ProductFetcherInterface;

final readonly class QueryHandler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private ProductFetcherInterface $fetcher,
    ) {}

    public function handle(Query $query): array
    {
        $products = $this->fetcher->getAllByCourseIds($query->courseIds);

        return array_map(static fn (array $data): ProductDTO => ProductDTO::fromArray($data), $products);
    }
}
