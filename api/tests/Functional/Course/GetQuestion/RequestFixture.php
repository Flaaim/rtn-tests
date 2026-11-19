<?php

declare(strict_types=1);

namespace Tests\Functional\Course\GetQuestion;

use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\Question;
use App\Course\Entity\Course\QuestionForm;
use App\Course\Test\Builder\CourseBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture implements DependentFixtureInterface
{
    public const string QUESTION_ID = 'b011b93e-4918-4ba6-8cc0-0a0f1bbcb134';
    public const string QUESTION_PROTECTED_ID = 'f546cdc2-7f0f-4d12-a039-3878baaf0206';
    public const string QUESTION_NOT_FOUND_ID = 'f3ad4954-8d19-4720-bbac-f708b07a199e';
    public const string COURSE_ID = '63879491-6883-4e88-8be2-295d3d260346';
    public const string COURSE_PROTECTED_ID = '984d3b44-de64-489a-b2cc-31996cb87379';
    public const string COURSE_NOT_FOUND_ID = '9feaa6c5-69b4-473b-8e00-25a818917b82';

    public function load(ObjectManager $manager): void
    {
        $course = new CourseBuilder()
            ->withCourseId(new CourseId(self::COURSE_ID))
            ->withQuestions([$this->getQuestion(self::QUESTION_ID)])
            ->build();
        $manager->persist($course);

        $protectedCourse = new CourseBuilder()
            ->withCourseId(new CourseId(self::COURSE_PROTECTED_ID))
            ->withQuestions([$this->getQuestion(self::QUESTION_PROTECTED_ID)])
            ->protect()
            ->build();
        $manager->persist($protectedCourse);

        $notFoundCourse = new CourseBuilder()
            ->withCourseId(new CourseId(self::COURSE_NOT_FOUND_ID))
            ->withQuestions([$this->getQuestion(self::QUESTION_NOT_FOUND_ID)])
            ->build();
        $manager->persist($notFoundCourse);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TestFixture::class,
            CategoryFixture::class,
            ProductFixture::class,
        ];
    }

    private function getQuestion(string $id): Question
    {
        return new Question(
            $id,
            'some text',
            '',
            [
                [
                    'id' => 'bbc14085f1e34ca93ccbbbd5ee9b5a01',
                    'text' => 'Потормошить пострадавшего за плечи',
                    'isCorrect' => false,
                    'answerImg' => '',
                ],
                [
                    'id' => '93ff5fdd3e7eeb5cc38696beac126968',
                    'text' => 'Придать пострадавшему устойчивое боковое положение',
                    'isCorrect' => true,
                    'answerImg' => '',
                ],
            ],
            QuestionForm::singleChoice()
        );
    }
}
