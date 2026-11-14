<?php

declare(strict_types=1);

namespace App\Event\Query\Event\GetPaginated;

final readonly class ListEventDTO
{
    public function __construct(
        public array $items,
        public int $totalCount,
        public int $totalPages,
    ) {}
}
