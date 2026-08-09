<?php

declare(strict_types=1);

namespace App\Testing\Service;

use App\Testing\Entity\Course\Question;
use App\Testing\Entity\Course\Type;

final class QuestionTypeResolver
{
    public function resolve(array $questions): void
    {
        /** @var Question $question */
        foreach ($questions as $question) {
            $totalAnswers = \count($question->getAnswers());
            $correctedAnswers = 0;

            foreach ($question->getAnswers() as $answer) {
                if (true === $answer->isCorrect()) {
                    ++$correctedAnswers;
                }
            }

            $text = mb_strtolower($question->getText());

            if ($correctedAnswers === $totalAnswers) {
                if (
                    str_contains($text, 'установите последовательность') ||
                    str_contains($text, 'установите правильную последовательность') ||
                    str_contains($text, 'порядок действий')
                ) {
                    $question->resolveType(Type::sequence());
                    continue;
                }

                if (str_contains($text, 'установите соответствие')) {
                    $question->resolveType(Type::matching());
                    continue;
                }
            }

            if (1 === $correctedAnswers) {
                $question->resolveType(Type::singleChoice());
            } elseif ($correctedAnswers > 1) {
                $question->resolveType(Type::multipleChoice());
            } else {
                // Фолбэк на случай ошибки парсера (если 0 правильных ответов)
                $question->resolveType(Type::singleChoice());
            }
        }
    }
}
