<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetQuestionsByCourseIds;

use App\Course\Query\Course\CourseFetcherInterface;
use DomainException;

/** @psalm-suppress UnusedClass */
final class QueryHandler
{
    public function __construct(
        private readonly CourseFetcherInterface $courses
    ) {}

    /**
     *@return QuestionDTO[]
     */
    public function handle(Query $query): array
    {
        $questions = $this->courses->getQuestionsByCourseIds($query->courseIds);
        if (empty($questions)) {
            throw new DomainException('No questions found.');
        }

        return array_map(
            static fn (array $question): QuestionDTO => QuestionDTO::fromArray($question),
            $questions
        );
    }
}
