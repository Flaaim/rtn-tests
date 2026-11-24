<?php

declare(strict_types=1);

namespace App\Testing\Interfaces;

interface ProductInterface
{
    public function getProductsByCourseIds(array $courseIds): array;
}
