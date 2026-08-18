<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetQuestionsIds;

use App\Course\Query\Course\CourseFetcherInterface;
use DomainException;

final class QueryHandler
{
    public function __construct(
        private readonly CourseFetcherInterface $courses
    ) {}

    public function handle(Query $query): array
    {
        $questionIds = $this->courses->getQuestionIdsByCourseId($query->id);
        if (empty($questionIds)) {
            throw new DomainException('QuestionIds not found.');
        }
        return $questionIds;
    }
}
