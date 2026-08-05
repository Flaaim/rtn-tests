<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Entity;

use App\Testing\Entity\Question;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class QuestionTest extends TestCase
{
    public function testSuccess(): void
    {
        $question = new Question(
            $id = '0090e2f3-8f3c-4c4c-8441-d22f778f85ad',
            $text = 'Вопрос 1',
            $questionImg = 'https://image.com',
            []
        );

        self::assertEquals($id, $question->getId());
        self::assertEquals($text, $question->getText());
        self::assertEquals($questionImg, $question->getQuestionImg());
    }
}
