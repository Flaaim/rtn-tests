<?php

declare(strict_types=1);

namespace App\Course\Test\Unit\Entity\Course\Command\Protect;

use App\Course\Entity\Course\CourseId;
use App\Course\Event\CourseProtected;
use App\Course\Test\Builder\CourseBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class ProtectCourseTest extends TestCase
{
    public function testProtect(): void
    {
        $course = new CourseBuilder()
            ->withCourseId($id = CourseId::generate())
            ->withCipher($cipher = 'ОТ 201.18')
            ->build();

        $course->protect();
        self::assertTrue($course->isProtected());

        $messages = $course->releaseEvents();
        $message = $messages[0];

        self::assertInstanceOf(CourseProtected::class, $message);
        self::assertEquals($id, $message->courseId);
        self::assertEquals($cipher, $message->cipher);
    }

    public function testProtectedAlready(): void
    {
        $course = new CourseBuilder()
            ->withCourseId(CourseId::generate())
            ->withCipher('ОТ 201.18')
            ->protect()
            ->build();

        $course->protect();
        $messages = $course->releaseEvents();

        self::assertEmpty($messages);
    }
}
