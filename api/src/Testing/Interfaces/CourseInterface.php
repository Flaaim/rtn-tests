<?php

declare(strict_types=1);

namespace App\Testing\Interfaces;

interface CourseInterface
{
    public function getQuestionIds(string $courseId): array;
}
