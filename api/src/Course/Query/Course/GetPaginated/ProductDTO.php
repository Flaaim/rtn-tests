<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetPaginated;

use DateTimeImmutable;

final readonly class ProductDTO
{
    public function __construct(
        public string $productId,
        public string $addedAt,
        public string $amount
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productId: $data['productId'],
            addedAt: new DateTimeImmutable($data['addedAt'])->format('Y-m-d'),
            amount: $data['amount']
        );
    }
}
