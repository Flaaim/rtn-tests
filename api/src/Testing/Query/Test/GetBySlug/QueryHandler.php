<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetBySlug;

use App\Testing\Interfaces\ProductInterface;
use App\Testing\Query\Test\TestFetcherInterface;
use DomainException;

final readonly class QueryHandler
{
    public function __construct(
        private TestFetcherInterface $tests,
        private ProductInterface $products
    ) {}

    public function handle(Query $query): TestDTO
    {
        $test = $this->tests->getBySlug($query->slug);
        if (empty($test)) {
            throw new DomainException('No test found for slug ' . $query->slug);
        }

        $products = $this->products->getProductsByCourseIds($test['courseIds']);

        return TestDTO::create($test, $products);
    }
}
