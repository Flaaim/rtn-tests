<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetPaginated;

final class ListTestDTO
{
    public function __construct(
        /** @var TestDTO[] $items */
        public array $items,
        public int $totalCount,
        public int $totalPages,
    ) {}
}
