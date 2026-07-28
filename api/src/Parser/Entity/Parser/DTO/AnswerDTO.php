<?php

declare(strict_types=1);

namespace App\Parser\Entity\Parser\DTO;

use Ramsey\Uuid\Uuid;

final class AnswerDTO
{
    public function __construct(
        public string $id,
        public string $text,
        public bool $isCorrect,
        public string $answerImg
    ){}


    public static function fromArray(array $data): self
    {
        return new self(
            id: Uuid::uuid4()->toString(),
            text: $data['Text'],
            isCorrect: $data['Correct'],
            answerImg: $data['answerImg'],
        );
    }
}
