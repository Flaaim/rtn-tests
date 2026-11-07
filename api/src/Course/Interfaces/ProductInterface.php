<?php

declare(strict_types=1);

namespace App\Course\Interfaces;

interface ProductInterface
{
    public function getProductCourse(string $courseId): array;
}
