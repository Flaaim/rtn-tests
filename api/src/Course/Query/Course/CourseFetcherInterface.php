<?php

declare(strict_types=1);

namespace App\Course\Query\Course;

interface CourseFetcherInterface
{
    public function getPaginated(int $page = 1, int $limit = 15, ?string $search = null): array;

    public function getOneById(string $id): array;

    public function getQuestionIdsByCourseId(string $courseId): array;
}
