<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Product;

interface ProductFetcherInterface
{
    public function getOneById(string $id): array;
}
