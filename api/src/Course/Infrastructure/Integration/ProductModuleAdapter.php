<?php

declare(strict_types=1);

namespace App\Course\Infrastructure\Integration;

use App\Course\Interfaces\ProductInterface;
use App\CourseAnswers\Api\ProductApi;
use App\CourseAnswers\Query\Product\GetProducts\ProductDTO;

final readonly class ProductModuleAdapter implements ProductInterface
{
    public function __construct(
        private ProductApi $productApi
    ) {}

    public function getProductCourse(string $courseId): array
    {
        $productDTO = $this->productApi->getProductByCourseId($courseId);

        if (null === $productDTO) {
            return [];
        }
        return [
            'product' => [
                'productId' => $productDTO->productId,
                'amount' => $productDTO->amount,
                'currency' => $productDTO->currency,
                'filePath' => $productDTO->filePath,
                'addedAt' => $productDTO->addedAt,
            ],
        ];
    }

    public function getProducts(): array
    {
        /** @var ProductDTO[] $result */
        $result = $this->productApi->getProducts();

        $products = [];
        foreach ($result as $product) {
            $products[] = [
                'productId' => $product->productId,
                'cipher' => $product->cipher,
                'amount' => $product->amount,
                'currency' => $product->currency,
                'courseId' => $product->courseId,
                'addedAt' => $product->addedAt,
            ];
        }
        return $products;
    }
}
