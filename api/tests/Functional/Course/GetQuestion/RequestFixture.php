<?php

declare(strict_types=1);

namespace Tests\Functional\Course\GetQuestion;

use App\Course\Entity\Course\Course;
use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\Question;
use App\Course\Entity\Course\QuestionForm;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture implements DependentFixtureInterface
{
    public const string QUESTION_ID = 'b011b93e-4918-4ba6-8cc0-0a0f1bbcb134';
    public const string COURSE_ID = '63879491-6883-4e88-8be2-295d3d260346';

    public function load(ObjectManager $manager): void
    {
        $question = new Question(
            self::QUESTION_ID,
            'some text',
            '',
            [],
            QuestionForm::singleChoice()
        );

        $course = new Course(
            new CourseId(self::COURSE_ID),
            'Охрана труда',
            [$question],
            new DateTimeImmutable(),
            'ПББ 115.26'
        );
        $manager->persist($course);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TestFixture::class,
            CategoryFixture::class,
        ];
    }
}
