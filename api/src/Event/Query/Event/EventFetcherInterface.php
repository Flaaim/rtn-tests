<?php

declare(strict_types=1);

namespace App\Event\Query\Event;

interface EventFetcherInterface
{
    public function getPaginated(int $page = 1, int $limit = 15, ?string $search = null): array;
}
