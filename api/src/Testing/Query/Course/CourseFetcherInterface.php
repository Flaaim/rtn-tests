<?php

declare(strict_types=1);

namespace App\Testing\Query\Course;

interface CourseFetcherInterface
{
    public function findAll(): array;
}
