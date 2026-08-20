<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetQuestionsByCourseIds;

final class QuestionDTO
{
    /** @param AnswerDTO[] $answers */
    public function __construct(
        public string $id,
        public string $text,
        public string $questionImg,
        public array $answers,
        public string $form,
    ) {}

    public static function fromArray(array $data): self
    {
        $answers = array_map(
            static fn (array $answer) => AnswerDTO::fromArray($answer),
            $data['answers'] ?? []
        );

        return new self(
            id: $data['id'],
            text: $data['text'],
            questionImg: $data['question_img'] ?? '',
            answers: $answers,
            form: $data['form'],
        );
    }
}
