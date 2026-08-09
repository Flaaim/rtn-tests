<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Service;

use App\Testing\Entity\Course\Answer;
use App\Testing\Entity\Course\Question;
use App\Testing\Service\QuestionTypeResolver;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class QuestionResolverTest extends TestCase
{
    public function testSingleChoice(): void
    {
        $questions = [
            new Question(
                '56c0db71-38de-498d-962e-2adbe5e85527',
                'Single Choice',
                '',
                [
                    new Answer('8c4d77d0-4cd9-4e5b-bf72-042bb059024a', 'single choice answer1', true, ''),
                    new Answer('06633ec9-def9-4f1d-b908-c36f75c5c514', 'single choice answer2', false, ''),
                ]
            ),
        ];

        $resolver = new QuestionTypeResolver();
        $resolver->resolve($questions);

        self::assertEquals('single_choice', $questions[0]->getType()->getValue());
    }

    public function testMultipleChoice(): void
    {
        $questions = [
            new Question(
                '56c0db71-38de-498d-962e-2adbe5e85527',
                'Single Choice',
                '',
                [
                    new Answer('8c4d77d0-4cd9-4e5b-bf72-042bb059024a', 'single choice answer1', true, ''),
                    new Answer('06633ec9-def9-4f1d-b908-c36f75c5c514', 'single choice answer2', true, ''),
                    new Answer('06633ec9-def9-4f1d-b908-c36f75c5c514', 'single choice answer3', false, ''),
                ]
            ),
        ];
        $resolver = new QuestionTypeResolver();
        $resolver->resolve($questions);
        self::assertEquals('multiple_choice', $questions[0]->getType()->getValue());
    }

    public function testMatching(): void
    {
        $questions = [
            new Question(
                '56c0db71-38de-498d-962e-2adbe5e85527',
                'Установите соответствие',
                '',
                [
                    new Answer('8c4d77d0-4cd9-4e5b-bf72-042bb059024a', 'single choice answer1', true, ''),
                    new Answer('06633ec9-def9-4f1d-b908-c36f75c5c514', 'single choice answer2', true, ''),
                    new Answer('06633ec9-def9-4f1d-b908-c36f75c5c514', 'single choice answer3', true, ''),
                ]
            ),
        ];

        $resolver = new QuestionTypeResolver();
        $resolver->resolve($questions);
        self::assertEquals('matching', $questions[0]->getType()->getValue());
    }

    public function testSequence(): void
    {
        $questions = [
            new Question(
                '56c0db71-38de-498d-962e-2adbe5e85527',
                'Установите правильную последовательность',
                '',
                [
                    new Answer('8c4d77d0-4cd9-4e5b-bf72-042bb059024a', 'single choice answer1', true, ''),
                    new Answer('06633ec9-def9-4f1d-b908-c36f75c5c514', 'single choice answer2', true, ''),
                    new Answer('06633ec9-def9-4f1d-b908-c36f75c5c514', 'single choice answer3', true, ''),
                ]
            ),
        ];

        $resolver = new QuestionTypeResolver();
        $resolver->resolve($questions);
        self::assertEquals('sequence', $questions[0]->getType()->getValue());
    }

    public function testAllIsCorrectAndTextIsNotfound(): void
    {
        $questions = [
            new Question(
                '56c0db71-38de-498d-962e-2adbe5e85527',
                'Single Choice',
                '',
                [
                    new Answer('8c4d77d0-4cd9-4e5b-bf72-042bb059024a', 'single choice answer1', true, ''),
                    new Answer('06633ec9-def9-4f1d-b908-c36f75c5c514', 'single choice answer2', true, ''),
                    new Answer('06633ec9-def9-4f1d-b908-c36f75c5c514', 'single choice answer3', true, ''),
                ]
            ),
        ];

        $resolver = new QuestionTypeResolver();
        $resolver->resolve($questions);
        self::assertEquals('multiple_choice', $questions[0]->getType()->getValue());
    }
}
