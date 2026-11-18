<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetQuestions;

use App\Course\Query\Course\CourseFetcherInterface;
use DomainException;

final readonly class QueryHandler
{
    public function __construct(
        private CourseFetcherInterface $fetcher,
    ) {}

    public function handle(Query $query): array
    {
        $rows = $this->fetcher->getQuestions($query->questionIds);

        if (empty($rows)) {
            throw new DomainException('No questions found.');
        }

        return array_map(
            static fn (array $question) => QuestionDTO::fromArray($question),
            $rows
        );
    }
}
