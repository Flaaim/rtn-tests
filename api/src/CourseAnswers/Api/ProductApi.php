<?php

declare(strict_types=1);

namespace App\CourseAnswers\Api;

use App\CourseAnswers\Query\Product\GetByCourseId;
use App\CourseAnswers\Query\Product\GetByCourseId\ProductDTO;
use App\CourseAnswers\Query\Product\GetByCourseId\Query;
use App\CourseAnswers\Query\Product\GetProducts;

final readonly class ProductApi
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private GetByCourseId\QueryHandler $queryHandler,
        private GetProducts\QueryHandler $getProducts,
    ) {}

    public function getProductByCourseId(string $courseId): ?ProductDTO
    {
        return $this->queryHandler->handle(new Query($courseId));
    }

    public function getProducts(): array
    {
        return $this->getProducts->handle();
    }
}
