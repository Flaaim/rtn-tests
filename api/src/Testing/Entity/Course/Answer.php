<?php

declare(strict_types=1);

namespace App\Testing\Entity\Course;

use JsonSerializable;

final class Answer implements JsonSerializable
{
    public function __construct(
        private string $id,
        private string $text,
        private bool $isCorrect,
        private string $answerImg
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            text: $data['text'],
            isCorrect: $data['isCorrect'],
            answerImg: $data['answerImg'] ?? ''
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function isCorrect(): bool
    {
        return $this->isCorrect;
    }

    public function getAnswerImg(): string
    {
        return $this->answerImg;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'isCorrect' => $this->isCorrect,
            'answerImg' => $this->answerImg,
        ];
    }

    public function replaceAnswerImg(string $answerImg): void
    {
        $this->answerImg = $answerImg;
    }
}
