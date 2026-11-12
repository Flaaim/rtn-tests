<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Product\GetProducts;

use App\CourseAnswers\Query\Product\ProductFetcherInterface;

final readonly class QueryHandler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private ProductFetcherInterface $products
    ) {}

    public function handle(): array
    {
        $products = $this->products->getProducts();

        return array_map(
            static fn (array $data) => ProductDTO::fromArray($data),
            $products
        );
    }
}
