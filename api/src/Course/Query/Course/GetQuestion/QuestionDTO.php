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
        public TestDTO $test,
        public CategoryDTO $category,
    ) {}

    public static function fromArray(array $data): self
    {
        $test = TestDTO::fromArray($data['test']);

        $category = CategoryDTO::fromArray($data['category']);

        return new self(
            id: $data['id'],
            text: $data['text'],
            answers: $data['answers'],
            form: $data['form'],
            test: $test,
            category: $category
        );
    }
}
