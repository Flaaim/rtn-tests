<?php

declare(strict_types=1);

namespace App\Course\Api;

use App\Course\Query\Course\GetQuestionsIds\Query;
use App\Course\Query\Course\GetQuestionsIds\QueryHandler;

final class CourseApi
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly QueryHandler $queryHandler,
    ) {}

    public function getQuestionIds(string $courseId): array
    {
        return $this->queryHandler->handle(new Query($courseId));
    }
}
