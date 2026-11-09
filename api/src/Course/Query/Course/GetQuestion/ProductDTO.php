<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetQuestion;

final readonly class ProductDTO
{
    public function __construct(
        public string $productId,
        public string $amount,
        public string $addedAt
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productId: $data['productId'],
            amount: $data['amount'],
            addedAt: $data['addedAt']
        );
    }
}
