<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Product;

interface ProductFetcherInterface
{
    public function getOneById(string $id): array;

    public function getPaginated(int $page = 1, int $limit = 15, ?string $search = null): array;
}
