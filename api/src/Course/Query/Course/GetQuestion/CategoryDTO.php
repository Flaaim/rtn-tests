<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetQuestion;

final readonly class CategoryDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?array $parent = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'],
            parent: $data['parent'],
        );
    }
}
