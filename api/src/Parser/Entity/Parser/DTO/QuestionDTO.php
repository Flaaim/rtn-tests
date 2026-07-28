<?php

declare(strict_types=1);

namespace App\Parser\Entity\Parser\DTO;

final class QuestionDTO
{
    public function __construct(
        public string $id,
        public int $number,
        public string $text,
        public string $questionImg,
        public array $answers = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['Id'],
            number: $data['Number'],
            text: $data['Text'],
            questionImg: $data['QuestionMainImg'],
        );
    }
}
