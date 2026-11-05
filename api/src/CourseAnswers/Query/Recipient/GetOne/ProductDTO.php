<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Recipient\GetOne;

final readonly class ProductDTO
{
    public function __construct(
        public string $productId,
        public string $cipher,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productId: $data['productId'],
            cipher: $data['cipher'],
        );
    }
}
