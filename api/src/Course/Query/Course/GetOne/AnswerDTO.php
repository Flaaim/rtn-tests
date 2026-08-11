<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetOne;

final class AnswerDTO
{
    public function __construct(
        public string $id,
        public string $text,
        public bool $isCorrect,
        public string $answerImg,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            text: $data['text'],
            isCorrect: $data['isCorrect'],
            answerImg: $data['answerImg'] ?? '',
        );
    }
}
