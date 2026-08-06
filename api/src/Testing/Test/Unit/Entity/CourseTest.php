<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Entity;

use App\Testing\Entity\Course\Course;
use App\Testing\Entity\Course\CourseId;
use App\Testing\Entity\Course\Status;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
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
            $questions = new ArrayCollection([]),
            $createdAt = new DateTimeImmutable(),
            $cipher = 'ОТ 201.18'
        );

        self::assertEquals($courseId, $course->getCourseId());
        self::assertEquals($name, $course->getName());
        self::assertEquals($questions->toArray(), $course->getQuestions());
        self::assertEquals($createdAt, $course->getCreatedAt());
        self::assertEquals(Status::processing(), $course->getStatus());
        self::assertEquals($cipher, $course->getCipher());
    }

    public function testAddQuestions(): void {}
}
