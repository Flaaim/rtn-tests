<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetByCourseId;

final readonly class TestDTO
{
    public function __construct(
        public string $testId,
        public string $name,
        public string $description,
        public string $slug,
        public CategoryDTO $category,
    ) {}

    public static function fromArray(array $data): self
    {
        $category = CategoryDTO::fromArray($data['category']);

        return new self(
            testId: $data['test_id'],
            name: $data['name'],
            description: $data['description'],
            slug: $data['slug'],
            category: $category,
        );
    }
}
