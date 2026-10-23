<?php

declare(strict_types=1);

namespace App\Course\Api;

use App\Course\Query\Course\GetQuestionsIds\Query;
use App\Course\Query\Course\GetQuestionsIds\QueryHandler;
use App\Testing\Interfaces\CourseInterface;

final readonly class CourseApi implements CourseInterface
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private QueryHandler $queryHandler,
    ) {}

    public function getQuestionIds(string $courseId): array
    {
        return $this->queryHandler->handle(new Query($courseId));
    }
}
