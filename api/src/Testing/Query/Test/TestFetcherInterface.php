<?php

declare(strict_types=1);

namespace App\Testing\Query\Test;

interface TestFetcherInterface
{
    public function getPaginated(int $page = 1, int $limit = 15, ?string $search = null): array;
}
