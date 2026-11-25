<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Product\GetAllByCourseIds;

use DateTimeImmutable;

final readonly class ProductDTO
{
    public function __construct(
        public string $productId,
        public string $name,
        public string $amount,
        public string $addedAt
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productId: $data['productId'],
            name: $data['name'],
            amount: $data['amount'],
            addedAt: new DateTimeImmutable($data['addedAt'])->format('Y-m-d')
        );
    }
}
