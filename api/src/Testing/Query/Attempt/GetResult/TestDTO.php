<?php

declare(strict_types=1);

namespace App\Testing\Query\Attempt\GetResult;

final class TestDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $cipher,
        public int $allowedMistakes,
        public string $slug,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['test_id'],
            name: $data['name'],
            cipher: $data['cipher'],
            allowedMistakes: $data['allowed_mistakes'],
            slug: $data['slug'],
        );
    }
}
