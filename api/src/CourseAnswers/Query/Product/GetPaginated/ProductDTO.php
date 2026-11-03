<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Product\GetPaginated;

use DateTimeImmutable;

final readonly class ProductDTO
{
    public function __construct(
        public string $productId,
        public string $cipher,
        public string $amount,
        public string $currency,
        public string $addedAt
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productId: $data['product_id'],
            cipher: $data['cipher'],
            amount: $data['amount'],
            currency: $data['currency'],
            addedAt: new DateTimeImmutable($data['added_at'])->format('Y-m-d'),
        );
    }
}
