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

        $product = $this->product->getProductCourse($question['course']['courseId']);
        $test = $this->test->getTestByCourseId($question['course']['courseId']);

        if (null === $test) {
            throw new DomainException(\sprintf('Data integrity error: Test not found for courseId %s', $question['course']['courseId']));
        }

        return QuestionDTO::create($question, $product, $test);
    }
}
