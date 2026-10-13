<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetQuestion;

use App\Course\Query\Course\CourseFetcherInterface;
use DomainException;

final readonly class QueryHandler
{
    public function __construct(
        private CourseFetcherInterface $courses,
    ) {}

    public function handle(Query $query): QuestionDTO
    {
        $question = $this->courses->getQuestion($query->questionId);

        if (empty($question)) {
            throw new DomainException('Question not found.');
        }

        return QuestionDTO::fromArray($question);
    }
}
