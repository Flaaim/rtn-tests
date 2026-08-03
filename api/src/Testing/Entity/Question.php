<?php

declare(strict_types=1);

namespace App\Testing\Entity;

final class Question
{
    public function __construct(
        private string $id,
        private string $text,
        private string $questionImg,
        private array $answers,
    ){}


    public static function fromDraft(array $data): self
    {
        return new self(
            id: $data['id'],
            text: $data['text'],
            questionImg: $data['questionImg'],
            answers: $data['answers'],
        );
    }
}
