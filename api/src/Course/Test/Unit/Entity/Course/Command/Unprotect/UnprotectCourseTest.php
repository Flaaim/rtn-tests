<?php

declare(strict_types=1);

namespace App\Course\Test\Unit\Entity\Course\Command\Unprotect;

use App\Course\Entity\Course\CourseId;
use App\Course\Event\CourseUnprotected;
use App\Course\Test\Builder\CourseBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class UnprotectCourseTest extends TestCase
{
    public function testUnprotect(): void
    {
        $course = new CourseBuilder()
            ->withCourseId($id = CourseId::generate())
            ->withCipher($cipher = 'ОТ 201.18')
            ->protect()
            ->build();

        $course->unprotect();
        self::assertFalse($course->isProtected());

        $messages = $course->releaseEvents();
        $message = $messages[0];

        self::assertInstanceOf(CourseUnprotected::class, $message);
        self::assertEquals($id, $message->courseId);
        self::assertEquals($cipher, $message->cipher);
    }

    public function testUnprotectAlready(): void
    {
        $course = new CourseBuilder()
            ->withCourseId(CourseId::generate())
            ->withCipher('ОТ 201.18')
            ->build();

        $course->unprotect();

        $messages = $course->releaseEvents();
        self::assertEmpty($messages);
    }
}
