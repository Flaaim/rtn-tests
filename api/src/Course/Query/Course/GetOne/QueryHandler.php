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
        $course = $this->fetcher->getOneById($query->id);

        if (empty($course)) {
            throw new DomainException('Course not found.');
        }

        $product = $this->product->getProductCourse($course['course_id']);

        return CourseFullDTO::create($course, $product);
    }
}
