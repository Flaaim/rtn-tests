<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Test\SyncQuestions;

use App\Testing\Entity\Test\Settings;
use App\Testing\Entity\Test\TestId;
use App\Testing\Test\Builder\TestBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Tests\Functional\Admin\Course\Course\Get\RequestFixture as CourseGetRequestFixture;

final class RequestFixture extends AbstractFixture implements DependentFixtureInterface
{
    public const string TEST_ID = 'ff1f1a44-f5f5-4956-9a09-feef0d28cedd';
    public const string TEST_NOT_FOUND_ID = '96861841-ad71-467d-b32b-2f0e539de742';

    public const string TEST_ACTIVE_ID = '958467b2-37c1-4301-bb69-df3325aff49c';
    public const string TEST_NAME = 'Первая помощь';
    public const string TEST_CIPHER = 'ОТ 201.18';
    public const string TEST_CIPHER_ACTIVE = 'ПБ 115.26';

    public function load(ObjectManager $manager): void
    {
        $test = new TestBuilder()
            ->withId(new TestId(self::TEST_ID))
            ->withName(self::TEST_NAME)
            ->withSettings(new Settings(5, 2, 1))
            ->withCourseIds([CourseGetRequestFixture::COURSE_ID])
            ->withQuestionIds(CourseGetRequestFixture::QUESTION_IDS)
            ->build();
        $manager->persist($test);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CourseGetRequestFixture::class,
        ];
    }
}
