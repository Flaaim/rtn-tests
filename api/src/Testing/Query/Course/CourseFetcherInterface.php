<?php

declare(strict_types=1);

namespace App\Testing\Query\Course;

interface CourseFetcherInterface
{
    public function getPaginated(int $page = 1, int $limit = 15, ?string $search = null): array;
}
