<?php

declare(strict_types=1);

namespace App\Parser\Test\Unit\Entity\Parser;

use App\Parser\Entity\Parser\Course;
use PHPUnit\Framework\TestCase;

final class CourseTest extends TestCase
{
    public function testSuccess(): void
    {
        $course = new Course(['key' => 'value']);
        self::assertEquals('value', $course->get('key'));
    }
    public function testEmpty(): void
    {
        self::expectException(\InvalidArgumentException::class);
        new Course([]);
    }
}
