<?php

declare(strict_types=1);

namespace App\Course\Infrastructure\Integration;

use App\Course\Interfaces\ProductInterface;
use App\CourseAnswers\Api\ProductApi;

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
}
