<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Product\GetByCourseId;

use App\CourseAnswers\Query\Product\ProductFetcherInterface;

final readonly class QueryHandler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private ProductFetcherInterface $products,
    ) {}

    public function handle(Query $query): ?ProductDTO
    {
        $row = $this->products->getOneByCourseId($query->courseId);
        if (empty($row)) {
            return null;
        }

        return ProductDTO::fromArray($row);
    }
}
