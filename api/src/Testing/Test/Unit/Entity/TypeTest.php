<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Entity;

use App\Testing\Entity\Course\Type;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class TypeTest extends TestCase
{
    public function testSingleChoice(): void
    {
        $type = Type::singleChoice();
        self::assertEquals('single_choice', $type->getValue());
        self::assertTrue($type->isSingleChoice());
    }

    public function testMultipleChoice(): void
    {
        $type = Type::multipleChoice();
        self::assertEquals('multiple_choice', $type->getValue());
        self::assertTrue($type->isMultipleChoice());
    }

    public function testMatching(): void
    {
        $type = Type::matching();
        self::assertEquals('matching', $type->getValue());
        self::assertTrue($type->isMatching());
    }

    public function testSequence(): void
    {
        $type = Type::sequence();
        self::assertEquals('sequence', $type->getValue());
        self::assertTrue($type->isSequence());
    }

    public function testEmpty(): void
    {
        self::expectException(InvalidArgumentException::class);
        new Type('');
    }

    public function testInvalidType(): void
    {
        self::expectException(InvalidArgumentException::class);
        new Type('string');
    }
}
