<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetOne;

use App\Course\Interfaces\ProductInterface;
use App\Course\Query\Course\CourseFetcherInterface;
use DomainException;

/** @psalm-suppress UnusedClass */
final readonly class QueryHandler
{
    public function __construct(
        private CourseFetcherInterface $fetcher,
        private ProductInterface $product,
    ) {}

    public function handle(Query $query): CourseFullDTO
    {
        $row = $this->fetcher->getOneById($query->id);

        if (empty($row)) {
            throw new DomainException('Course not found.');
        }

        $product = $this->product->getProductCourse($row['course_id'] ?? '');

        $row = array_merge($row, $product);

        return CourseFullDTO::fromArray($row);
    }
}
