<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetQuestion;

final readonly class QuestionDTO
{
    public function __construct(
        public string $id,
        public string $text,
        public array $answers,
        public string $form,
        public CourseDTO $course,
        public ?TestDTO $test = null,
        public ?ProductDTO $product = null,
    ) {}

    public static function create(array $question, ?array $product, ?array $test): self
    {
        $course = CourseDTO::fromArray($question['course']);

        $answers = $question['answers'];

        if ($course->isProtected === true) {
            $answers = array_map(static function (array $answer) {
                unset($answer['isCorrect']);
                return $answer;
            }, $question['answers']);
        }

        return new self(
            id: $question['id'],
            text: $question['text'],
            answers: $answers,
            form: $question['form'],
            course: $course,
            test: null !== $test ? TestDTO::fromArray($test) : null,
            product: null !== $product ? ProductDTO::fromArray($product) : null,
        );
    }
}
