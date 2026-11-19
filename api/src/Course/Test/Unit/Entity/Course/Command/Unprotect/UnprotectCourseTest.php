<?php

declare(strict_types=1);

namespace App\Course\Test\Unit\Entity\Course\Command\Unprotect;

use App\Course\Entity\Course\Answer;
use App\Course\Entity\Course\Course;
use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\Question;
use App\Course\Entity\Course\QuestionForm;
use App\Course\Event\CourseUnprotected;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class UnprotectCourseTest extends TestCase
{
    public function testUnprotectAlready(): void
    {
        $course = new Course(
            CourseId::generate(),
            'Course name',
            $this->getQuestions(),
            new DateTimeImmutable(),
            'ОТ 201.18'
        );
        $course->releaseEvents();
        $course->unprotect();

        $messages = $course->releaseEvents();
        self::assertEmpty($messages);
    }

    public function testUnprotected(): void
    {
        $course = new Course(
            $id = CourseId::generate(),
            'Course name',
            $this->getQuestions(),
            new DateTimeImmutable(),
            $cipher = 'ОТ 201.18'
        );
        $course->protect();
        $course->releaseEvents();

        $course->unprotect();
        self::assertFalse($course->isProtected());

        $messages = $course->releaseEvents();
        $message = $messages[0];

        self::assertInstanceOf(CourseUnprotected::class, $message);
        self::assertEquals($id, $message->courseId);
        self::assertEquals($cipher, $message->cipher);
    }

    private function getQuestions(): array
    {
        return [
            new Question(
                '7cedc5c2-e9eb-4986-83c2-d6795c2e3ff6',
                'Вопрос 1',
                'https://olimpoks.hydroschool.ru/QuestionImages/c92099/9fef1bcf-9c6c-4010-a670-3dc105abc574/10/1.jpg',
                [
                    Answer::fromArray(
                        [
                            'id' => '234a678461729b0a6d96375a55c349bf',
                            'text' => 'Травмы живота и таза',
                            'isCorrect' => true,
                            'answerImg' => 'https://olimpoks.hydroschool.ru/QuestionImages/92cdb7c1-efee-4790-bed7-f194f02da614/9/3.jpg',
                        ]
                    ),
                ],
                QuestionForm::singleChoice()
            ),
            new Question(
                'e6616f72-0af2-4510-96bb-9ce92265a712',
                'Вопрос 2',
                'https://olimpoks.hydroschool.ru/QuestionImages/c92192/145d3d30-5398-478d-bbb9-82c820f8ac1f/9/1.jpg',
                [],
                QuestionForm::singleChoice()
            ),
        ];
    }
}
