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
    public const string COURSE_ID = '63879491-6883-4e88-8be2-295d3d260346';
    public const string COURSE_PROTECTED_ID = '984d3b44-de64-489a-b2cc-31996cb87379';

    public function load(ObjectManager $manager): void
    {
        $question = new Question(
            self::QUESTION_ID,
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

        $protectedQuestion = new Question(
            self::QUESTION_PROTECTED_ID,
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

        $course = new CourseBuilder()
            ->withCourseId(new CourseId(self::COURSE_ID))
            ->withQuestions([$question])
            ->build();
        $manager->persist($course);

        $protectedCourse = new CourseBuilder()
            ->withCourseId(new CourseId(self::COURSE_PROTECTED_ID))
            ->withQuestions([$protectedQuestion])
            ->protect()
            ->build();
        $manager->persist($protectedCourse);

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
}
