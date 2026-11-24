<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetBySlug;

use DateTimeImmutable;

final class TestDTO
{
    /** @param ProductDTO[] $products */
    public function __construct(
        public string $id,
        public string $name,
        public string $cipher,
        public string $description,
        public array $tickets,
        public string $slug,
        public string $createdAt,
        public string $status,
        public array $settings,
        public array $products
    ) {}

    public static function create(array $test, array $products = []): self
    {
        $products = array_map(static fn (array $product): ProductDTO => ProductDTO::fromArray($product), $products);

        return new self(
            id: $test['id'],
            name: $test['name'],
            cipher: $test['cipher'],
            description: $test['description'],
            tickets: $test['tickets'],
            slug: $test['slug'],
            createdAt: new DateTimeImmutable($test['createdAt'])->format('Y-m-d'),
            status: $test['status'],
            settings: $test['settings'] ?? [],
            products: $products,
        );
    }
}
