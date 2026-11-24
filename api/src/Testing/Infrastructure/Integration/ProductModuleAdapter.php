<?php

declare(strict_types=1);

namespace App\Testing\Infrastructure\Integration;

use App\CourseAnswers\Api\ProductApi;
use App\Testing\Interfaces\ProductInterface;

final readonly class ProductModuleAdapter implements ProductInterface
{
    public function __construct(
        private ProductApi $productApi,
    ) {}

    public function getProductsByCourseIds(array $courseIds): array
    {
        return $this->productApi->getAllByCourseIds($courseIds);
    }
}
