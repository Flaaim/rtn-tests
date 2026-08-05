<?php

declare(strict_types=1);

namespace App\Testing\Entity;

final class Question
{
    /**
     * @param Answer[] $answers
     */
    public function __construct(
        private string $id,
        private string $text,
        private string $questionImg,
        private array $answers,
    ) {}

    public static function fromDraft(array $data): self
    {
        $answers = array_map(static fn (array $answerData) => Answer::fromArray($answerData), $data['answers']);
        return new self(
            id: $data['id'],
            text: $data['text'],
            questionImg: $data['questionImg'],
            answers: $answers,
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

    public function getQuestionImg(): string
    {
        return $this->questionImg;
    }

    /**
     * @return Answer[]
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    public function replaceQuestionImg(string $imgPath): void
    {
        $this->questionImg = $imgPath;
    }
}
