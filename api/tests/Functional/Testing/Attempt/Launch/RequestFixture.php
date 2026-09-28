<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\Launch;

use App\Testing\Entity\Test\Settings;
use App\Testing\Entity\Test\TestId;
use App\Testing\Test\Builder\TestBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Tests\Functional\Admin\Course\Course\Get\RequestFixture as CourseGetRequestFixture;

final class RequestFixture extends AbstractFixture implements DependentFixtureInterface
{
    public const string TEST_ID = '5c77e4dc-f1a2-4e6d-b1cd-9a7bdfa548ce';
    public const string TEST_NOT_FOUND = '581cae87-f0b5-4994-a6aa-5821fa2b963a';
    public const string TEST_NAME = 'Первая помощь';
    public const string TEST_CIPHER = 'ОТ 201.18';
    public const int TICKET_NUMBER = 1;

    public function load(ObjectManager $manager): void
    {
        $test = new TestBuilder()
            ->withId(new TestId(self::TEST_ID))
            ->withName(self::TEST_NAME)
            ->withCipher(self::TEST_CIPHER)
            ->withDescription(self::TEST_NAME)
            ->withSettings(new Settings(5, 2, 1))
            ->withCourseIds([CourseGetRequestFixture::COURSE_ID])
            ->withQuestionIds(CourseGetRequestFixture::QUESTION_IDS)
            ->active()
            ->build();
        $manager->persist($test);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CourseGetRequestFixture::class,
            SubscriptionFixture::class,
        ];
    }
}
