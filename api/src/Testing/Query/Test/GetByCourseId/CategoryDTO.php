<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetByCourseId;

final readonly class CategoryDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public ?array $parent = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            slug: $data['slug'],
            parent: $data['parent']
        );
    }
}
