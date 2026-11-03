<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Product\GetOne;

use App\CourseAnswers\Query\Product\ProductFetcherInterface;
use DomainException;

final readonly class QueryHandler
{
    public function __construct(
        private ProductFetcherInterface $products,
    ) {}

    public function handle(Query $query): ProductDTO
    {
        $row = $this->products->getOneById($query->id);
        if (empty($row)) {
            throw new DomainException('Product not found.');
        }

        return ProductDTO::fromArray($row);
    }
}
