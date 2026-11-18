<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetQuestion;

use App\Course\Interfaces\ProductInterface;
use App\Course\Interfaces\TestInterface;
use App\Course\Query\Course\CourseFetcherInterface;
use DomainException;

final readonly class QueryHandler
{
    public function __construct(
        private CourseFetcherInterface $courses,
        private ProductInterface $product,
        private TestInterface $test,
    ) {}

    public function handle(Query $query): QuestionDTO
    {
        $question = $this->courses->getQuestion($query->questionId);

        if (empty($question)) {
            throw new DomainException('Question not found.');
        }

        $product = $this->product->getProductCourse($question['course_id']);
        $test = $this->test->getTestByCourseId($question['course_id']);

        if (null === $test) {
            throw new DomainException(\sprintf('Data integrity error: Test not found for course_id "%s"', $question['course_id']));
        }

        return QuestionDTO::create($question, $product, $test);
    }
}
