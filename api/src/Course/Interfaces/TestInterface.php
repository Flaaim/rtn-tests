<?php

declare(strict_types=1);

namespace App\Course\Interfaces;

interface TestInterface
{
    public function getTestByCourseId(string $courseId): ?array;
}
