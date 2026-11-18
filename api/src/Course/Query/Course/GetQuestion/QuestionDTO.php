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
        public ?TestDTO $test = null,
        public ?ProductDTO $product = null,
    ) {}

    public static function create(array $question, ?array $product, ?array $test): self
    {
        return new self(
            id: $question['id'],
            text: $question['text'],
            answers: $question['answers'],
            form: $question['form'],
            test: null !== $test ? TestDTO::fromArray($test) : null,
            product: null !== $product ? ProductDTO::fromArray($product) : null,
        );
    }
}
