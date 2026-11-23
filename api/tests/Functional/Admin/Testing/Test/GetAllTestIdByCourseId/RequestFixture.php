<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Test\GetAllTestIdByCourseId;

use App\Course\Entity\Course\CourseId;
use App\Course\Test\Builder\CourseBuilder;
use App\Testing\Entity\Test\TestId;
use App\Testing\Test\Builder\TestBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string TEST_ID = '2a52f226-b514-4335-acd3-873c4771a97b';
    public const string TEST_NAME = 'Первая помощь';
    public const string TEST_CIPHER = 'ОТ 201.18';

    public const string COURSE_ID = '1c4e9a20-f839-4f00-a93c-ea851de49777';

    public function load(ObjectManager $manager): void
    {
        $course = new CourseBuilder()
            ->withCourseId(new CourseId(self::COURSE_ID))
            ->build();
        $manager->persist($course);

        $test = new TestBuilder()
            ->withId(new TestId(self::TEST_ID))
            ->withName(self::TEST_NAME)
            ->withCipher(self::TEST_CIPHER)
            ->withCourseIds([$course->getCourseId()->getValue()])
            ->build();
        $manager->persist($test);

        $manager->flush();
    }
}
