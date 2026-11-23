<?php

declare(strict_types=1);

namespace App\Course\Test\Unit\Entity\Course\Command\UpdateQuestions;

use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\Question;
use App\Course\Entity\Course\QuestionForm;
use App\Course\Event\CourseProcessed;
use App\Course\Event\CourseQuestionsUpdated;
use App\Course\Test\Builder\CourseBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class UpdateQuestionsCourseTest extends TestCase
{
    public function testUpdateQuestions(): void
    {
        $courseId = 'df1c3238-6501-4728-bc83-f92e785f2c1e';
        $questions = $this->getQuestions();

        $course = new CourseBuilder()
            ->withCourseId(new CourseId($courseId))
            ->withQuestions($questions)
            ->build();

        $newQuestions = $this->getNewQuestions();
        $course->addQuestions($newQuestions);

        self::assertCount(1, $course->getQuestions());
        self::assertEquals($newQuestions, $course->getQuestions());

        /** @var Question $question */
        $question = $course->getQuestions()[0];
        self::assertEquals($question->getCourse(), $course);

        $messages = $course->releaseEvents();

        $courseProcessedMessages = array_filter($messages, static fn ($message) => $message instanceof CourseProcessed);
        $courseProcessedMessage = end($courseProcessedMessages);

        self::assertInstanceOf(CourseProcessed::class, $courseProcessedMessage);
        self::assertEquals($courseId, $courseProcessedMessage->courseId);

        $courseQuestionsUpdatedMessages = array_filter($messages, static fn ($message) => $message instanceof CourseQuestionsUpdated);
        $courseQuestionsUpdatedMessage = end($courseQuestionsUpdatedMessages);

        self::assertInstanceOf(CourseQuestionsUpdated::class, $courseQuestionsUpdatedMessage);
        self::assertEquals($courseId, $courseQuestionsUpdatedMessage->courseId);
    }

    private function getQuestions(): array
    {
        return [
            new Question(
                '1872768c-65ee-4b03-8d01-0fe8f91da2c9',
                'Question text',
                '',
                [],
                QuestionForm::singleChoice()
            ),
        ];
    }

    private function getNewQuestions(): array
    {
        return [
            new Question(
                'dddbdff1-4228-402b-93c1-73e23f686f7c',
                'New Question',
                '',
                [],
                QuestionForm::singleChoice()
            ),
        ];
    }
}
