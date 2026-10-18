<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\GetCompetencyMatrix;

use App\Testing\Entity\Test\Settings;
use App\Testing\Entity\Test\TestId;
use App\Testing\Test\Builder\TestBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class TestFixture extends AbstractFixture
{
    public const string TEST_ID = '5c77e4dc-f1a2-4e6d-b1cd-9a7bdfa548ce';
    public const string TEST_NAME = 'Первая помощь';
    public const string TEST_CIPHER = 'ОТ 201.18';
    public const int TICKET_NUMBER = 1;

    public const string COURSE_ID = '63879491-6883-4e88-8be2-295d3d260346';
    public const string CATEGORY_ID = '753ac2cb-6f03-4c7d-a68c-f14646503c67';
    public const string CATEGORY_PARENT_ID = '1d655786-d282-4b08-ae39-257408dd7ed9';
    public const string CATEGORY_NAME = 'Охрана труда';

    public function load(ObjectManager $manager): void
    {
        $test = new TestBuilder()
            ->withId(new TestId(self::TEST_ID))
            ->withCategoryId(self::CATEGORY_ID)
            ->withName(self::TEST_NAME)
            ->withCipher(self::TEST_CIPHER)
            ->withDescription(self::TEST_NAME)
            ->withSettings(new Settings(5, 2, 1))
            ->withCourseIds([self::COURSE_ID])
            ->withQuestionIds(['23ca06c6-6d2d-45d6-ae89-0526afdff6f4', '16b1df62-5ad0-4707-ab1c-746affd6670f'])
            ->active()
            ->build();
        $manager->persist($test);

        $manager->flush();
    }
}
