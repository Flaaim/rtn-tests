<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetAllTestIdByCourseId;

final readonly class TestDTO
{
    public function __construct(
        public string $testId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            testId: $data['id']
        );
    }
}
