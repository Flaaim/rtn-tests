<?php

declare(strict_types=1);

namespace App\Testing\Infrastructure\Integration;

use App\CourseAnswers\Api\ProductApi;
use App\CourseAnswers\Query\Product\GetAllByCourseIds\ProductDTO;
use App\Testing\Interfaces\ProductInterface;

final readonly class ProductModuleAdapter implements ProductInterface
{
    public function __construct(
        private ProductApi $productApi,
    ) {}

    public function getProductsByCourseIds(array $courseIds): array
    {
        $products = $this->productApi->getAllByCourseIds($courseIds);

        return array_map(static fn (ProductDTO $product) => [
            'productId' => $product->productId,
            'name' => $product->name,
            'amount' => $product->amount,
            'addedAt' => $product->addedAt, ], $products);
    }
}
