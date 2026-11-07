<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Product\GetOne;

use DateTimeImmutable;

final readonly class ProductDTO
{
    public function __construct(
        public string $productId,
        public string $cipher,
        public string $courseId,
        public string $amount,
        public string $currency,
        public string $filePath,
        public string $addedAt
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productId: $data['product_id'],
            cipher: $data['cipher'],
            courseId: $data['course_id'],
            amount: $data['amount'],
            currency: $data['currency'],
            filePath: $data['file_path'],
            addedAt: new DateTimeImmutable($data['added_at'])->format('Y-m-d')
        );
    }
}
