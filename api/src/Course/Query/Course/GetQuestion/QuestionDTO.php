<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetQuestion;

use App\Course\Query\Course\GetOne\ProductDTO;

final readonly class QuestionDTO
{
    public function __construct(
        public string $id,
        public string $text,
        public array $answers,
        public string $form,
        public TestDTO $test,
        public CategoryDTO $category,
        public ?ProductDTO $product = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $test = TestDTO::fromArray($data['test']);

        $category = CategoryDTO::fromArray($data['category']);

        $product = null;

        if (isset($data['product'])) {
            $product = ProductDTO::fromArray($data['product']);
        }

        return new self(
            id: $data['id'],
            text: $data['text'],
            answers: $data['answers'],
            form: $data['form'],
            test: $test,
            category: $category,
            product: $product,
        );
    }
}
