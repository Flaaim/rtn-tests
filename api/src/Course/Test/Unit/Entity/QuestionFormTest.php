<?php

declare(strict_types=1);

namespace App\Course\Test\Unit\Entity;

use App\Course\Entity\Course\QuestionForm;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class QuestionFormTest extends TestCase
{
    public function testSingleChoice(): void
    {
        $type = QuestionForm::singleChoice();
        self::assertEquals('single_choice', $type->getValue());
        self::assertTrue($type->isSingleChoice());
    }

    public function testMultipleChoice(): void
    {
        $type = QuestionForm::multipleChoice();
        self::assertEquals('multiple_choice', $type->getValue());
        self::assertTrue($type->isMultipleChoice());
    }

    public function testMatching(): void
    {
        $type = QuestionForm::matching();
        self::assertEquals('matching', $type->getValue());
        self::assertTrue($type->isMatching());
    }

    public function testSequence(): void
    {
        $type = QuestionForm::sequence();
        self::assertEquals('sequence', $type->getValue());
        self::assertTrue($type->isSequence());
    }

    public function testEmpty(): void
    {
        self::expectException(InvalidArgumentException::class);
        new QuestionForm('');
    }

    public function testInvalidType(): void
    {
        self::expectException(InvalidArgumentException::class);
        new QuestionForm('string');
    }
}
