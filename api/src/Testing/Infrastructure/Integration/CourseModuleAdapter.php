<?php

declare(strict_types=1);

namespace App\Testing\Infrastructure\Integration;

use App\Course\Api\CourseApi;
use App\Testing\Interfaces\CourseInterface;

final readonly class CourseModuleAdapter implements CourseInterface
{
    public function __construct(
        private CourseApi $courseApi,
    ) {}

    public function getQuestionIds(string $courseId): array
    {
        return $this->courseApi->getQuestionIds($courseId);
    }

    public function getQuestions(array $questionIds): array
    {
        return $this->courseApi->getQuestions($questionIds);
    }
}
