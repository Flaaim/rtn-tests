<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetOne;

final readonly class ProductDTO
{
    public function __construct(
        public string $amount,
        public string $currency,
        public string $filePath,
        public string $addedAt
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            amount: $data['amount'],
            currency: $data['currency'],
            filePath: $data['filePath'],
            addedAt: $data['addedAt']
        );
    }
}
