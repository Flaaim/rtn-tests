<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Entity;

use App\Testing\Entity\Course\Course;
use App\Testing\Entity\Course\CourseId;
use App\Testing\Entity\Course\Question;
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

    public function testRename(): void
    {
        $course = new Course(
            CourseId::generate(),
            'Course name',
            new ArrayCollection([]),
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
            new ArrayCollection([]),
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
            new ArrayCollection([]),
            new DateTimeImmutable(),
            'ОТ 201.18'
        );
        $course->rename($name = 'New Course Name');

        self::assertEquals($name, $course->getName());
    }

    public function testUpdateQuestions(): void
    {
        $questions = [
            new Question('1872768c-65ee-4b03-8d01-0fe8f91da2c9', 'Question text', '', []),
        ];

        $course = new Course(
            CourseId::generate(),
            'Course name',
            new ArrayCollection($questions),
            new DateTimeImmutable(),
            'ОТ 201.18'
        );

        $newQuestions = [
            new Question('dddbdff1-4228-402b-93c1-73e23f686f7c', 'New Question', '', []),
        ];

        $course->addQuestions(new ArrayCollection($newQuestions));

        self::assertEquals($newQuestions, $course->getQuestions());

        /** @var Question $question */
        $question = $course->getQuestions()[0];
        self::assertEquals($question->getCourse(), $course);
    }
}
