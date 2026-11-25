<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Test\GetBySlug;

use App\Testing\Entity\Test\TestId;
use App\Testing\Test\Builder\TestBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Tests\Functional\Admin\Testing\Category\GetOne\RequestFixture as CategoryGetOneFixture;

final class RequestFixture extends AbstractFixture implements DependentFixtureInterface
{
    public const string TEST_ID = 'ff1f1a44-f5f5-4956-9a09-feef0d28cedd';
    public const string TEST_ACTIVE_ID = '958467b2-37c1-4301-bb69-df3325aff49c';

    public const string TEST_NAME = 'Первая помощь';
    public const string TEST_CIPHER = 'ОТ 201.18';
    public const string TEST_ACTIVE_NAME = 'Основы промышленной безопасности';
    public const string TEST_ACTIVE_CIPHER = 'ПБ 115.26';

    public const string TEST_SLUG = 'ot-201';
    public const string TEST_ACTIVE_SLUG = 'pb-115';

    public function load(ObjectManager $manager): void
    {
        $test = new TestBuilder()
            ->withId(new TestId(self::TEST_ID))
            ->withCategoryId(CategoryGetOneFixture::CATEGORY_ID)
            ->withName(self::TEST_NAME)
            ->withCipher(self::TEST_CIPHER)
            ->withSlug(self::TEST_SLUG)
            ->build();
        $manager->persist($test);

        $testActive = new TestBuilder()
            ->withId(new TestId(self::TEST_ACTIVE_ID))
            ->withCategoryId(CategoryGetOneFixture::CATEGORY_ID)
            ->withName(self::TEST_ACTIVE_NAME)
            ->withCipher(self::TEST_ACTIVE_CIPHER)
            ->withSlug(self::TEST_ACTIVE_SLUG)
            ->withCourseIds([CourseFixture::COURSE_ID])
            ->active()
            ->build();
        $manager->persist($testActive);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProductFixture::class,
            CourseFixture::class,
        ];
    }
}
