<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetQuestionsIds;

use App\Course\Query\Course\CourseFetcherInterface;

final class QueryHandler
{
    public function __construct(
        private readonly CourseFetcherInterface $courses
    ) {}

    public function handle(Query $query): array
    {
        return $this->courses->getQuestionIdsByCourseId($query->id);
    }
}
