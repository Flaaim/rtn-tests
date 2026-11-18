<?php

declare(strict_types=1);

namespace App\Course\Test\Unit\Entity\Question\Command\Protect;

use App\Course\Entity\Course\Question;
use App\Course\Entity\Course\QuestionForm;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class ProtectQuestionTest extends TestCase
{
    public function testProtect(): void
    {
        $question = new Question(
            '0090e2f3-8f3c-4c4c-8441-d22f778f85ad',
            'Вопрос 1',
            'https://image.com',
            [],
            QuestionForm::singleChoice()
        );

        $question->protect();

        self::assertTrue($question->isProtected());
    }
}
