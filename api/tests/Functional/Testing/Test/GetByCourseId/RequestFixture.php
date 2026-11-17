<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Test\GetByCourseId;

use App\Course\Entity\Course\Course;
use App\Course\Entity\Course\CourseId;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Tests\Functional\Course\GetQuestion\TestFixture;

final class RequestFixture extends AbstractFixture implements DependentFixtureInterface
{
    public const string COURSE_ID = '63879491-6883-4e88-8be2-295d3d260346';
    public const string COURSE_NOT_FOUND_ID = '629c2958-a340-4cfa-8c9c-1802fa02e391';

    public function load(ObjectManager $manager): void
    {
        $course = new Course(
            new CourseId(self::COURSE_ID),
            'Охрана труда',
            [],
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
