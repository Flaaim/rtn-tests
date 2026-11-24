<?php

declare(strict_types=1);

namespace App\CourseAnswers\Api;

use App\CourseAnswers\Query\Product\GetAllByCourseIds;
use App\CourseAnswers\Query\Product\GetAllByCourseIds\Query as GetAllByCourseIdsQuery;
use App\CourseAnswers\Query\Product\GetByCourseId;
use App\CourseAnswers\Query\Product\GetByCourseId\ProductDTO;
use App\CourseAnswers\Query\Product\GetByCourseId\Query as GetByCourseIdQuery;
use App\CourseAnswers\Query\Product\GetProducts;

final readonly class ProductApi
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private GetByCourseId\QueryHandler $queryHandler,
        private GetProducts\QueryHandler $getProducts,
        private GetAllByCourseIds\QueryHandler $getAllByCourseIds,
    ) {}

    public function getProductByCourseId(string $courseId): ?ProductDTO
    {
        return $this->queryHandler->handle(new GetByCourseIdQuery($courseId));
    }

    public function getProducts(): array
    {
        return $this->getProducts->handle();
    }

    public function getAllByCourseIds(array $courseIds): array
    {
        return $this->getAllByCourseIds->handle(new GetAllByCourseIdsQuery($courseIds));
    }
}
