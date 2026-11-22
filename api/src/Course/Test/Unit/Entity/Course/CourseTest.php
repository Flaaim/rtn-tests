<?php

declare(strict_types=1);

namespace App\Course\Test\Unit\Entity\Course;

use App\Course\Entity\Course\Course;
use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\Status;
use App\Course\Event\CourseProcessed;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class CourseTest extends TestCase
{
    public function testSuccess(): void
    {
        $course = new Course(
            $courseId = CourseId::generate(),
            $name = 'Course name',
            $questions = [],
            $createdAt = new DateTimeImmutable(),
            $cipher = 'ОТ 201.18'
        );

        self::assertEquals($courseId, $course->getCourseId());
        self::assertEquals($name, $course->getName());
        self::assertEquals($questions, $course->getQuestions());
        self::assertEquals($createdAt, $course->getCreatedAt());
        self::assertEquals(Status::processing(), $course->getStatus());
        self::assertEquals($cipher, $course->getCipher());

        $messages = $course->releaseEvents();
        $message = end($messages);

        self::assertInstanceOf(CourseProcessed::class, $message);
    }

    public function testRename(): void
    {
        $course = new Course(
            CourseId::generate(),
            'Course name',
            [],
            new DateTimeImmutable(),
            'ОТ 201.18'
        );

        $course->rename($name = 'New Course Name', $cipher = 'ОТ 203.18');
        self::assertEquals($name, $course->getName());
        self::assertEquals($cipher, $course->getCipher());
    }

    public function testRenameNoChanges(): void
    {
        $course = new Course(
            CourseId::generate(),
            $name = 'Course name',
            [],
            new DateTimeImmutable(),
            $cipher = 'ОТ 201.18'
        );
        $course->rename('Course name', 'ОТ 201.18');

        self::assertEquals($name, $course->getName());
        self::assertEquals($cipher, $course->getCipher());

        $course->rename();
        self::assertEquals($name, $course->getName());
        self::assertEquals($cipher, $course->getCipher());
    }

    public function testRenameName(): void
    {
        $course = new Course(
            CourseId::generate(),
            'Course name',
            [],
            new DateTimeImmutable(),
            'ОТ 201.18'
        );
        $course->rename($name = 'New Course Name');

        self::assertEquals($name, $course->getName());
    }
}
