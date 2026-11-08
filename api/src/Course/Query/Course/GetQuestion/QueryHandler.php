<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetQuestion;

use App\Course\Interfaces\ProductInterface;
use App\Course\Query\Course\CourseFetcherInterface;
use DomainException;

final readonly class QueryHandler
{
    public function __construct(
        private CourseFetcherInterface $courses,
        private ProductInterface $product,
    ) {}

    public function handle(Query $query): QuestionDTO
    {
        $question = $this->courses->getQuestion($query->questionId);

        if (empty($question)) {
            throw new DomainException('Question not found.');
        }

        $product = $this->product->getProductCourse($question['course_id'] ?? '');

        $question = array_merge($question, $product);

        return QuestionDTO::fromArray($question);
    }
}
