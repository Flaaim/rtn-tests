<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetPaginated;

use DateTimeImmutable;

final class TestDTO
{
    public function __construct(
        public string $testId,
        public string $name,
        public string $cipher,
        public string $status,
        public string $createdAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            testId: $data['id'],
            name: $data['name'],
            cipher: $data['cipher'],
            status: $data['status'],
            createdAt: new DateTimeImmutable()->format('Y-m-d'),
        );
    }
}
