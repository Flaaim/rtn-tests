<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Product\GetPaginated;

final class ListProductDTO
{
    public function __construct(
        /** @var ProductDTO[] $items */
        public array $items,
        public int $totalCount,
        public int $totalPages,
    ) {}
}
