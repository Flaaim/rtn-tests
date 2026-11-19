<?php

declare(strict_types=1);

namespace Tests\Functional\Course\GetQuestion;

use App\Testing\Entity\Test\TestId;
use App\Testing\Test\Builder\TestBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class TestFixture extends AbstractFixture
{
    public const string TEST_ID = '02ee8e2b-f535-4f96-bbe6-c6120a53b315';
    public const string TEST_PROTECTED_ID = '7e228402-7e09-4a18-99d7-893b077503c5';

    public function load(ObjectManager $manager): void
    {
        $test = new TestBuilder()
            ->withId(new TestId(self::TEST_ID))
            ->withCategoryId(CategoryFixture::CATEGORY_ID)
            ->withName('Основы промбезопасности')
            ->withCipher('ПББ 115.26')
            ->withSlug('pbb115')
            ->withCourseIds([RequestFixture::COURSE_ID])
            ->build();
        $manager->persist($test);

        $protected = new TestBuilder()
            ->withId(new TestId(self::TEST_PROTECTED_ID))
            ->withCategoryId(CategoryFixture::CATEGORY_ID)
            ->withName('Основы промбезопасности')
            ->withCipher('ПББ 115.26')
            ->withSlug('gpn101')
            ->withCourseIds([RequestFixture::COURSE_PROTECTED_ID])
            ->build();
        $manager->persist($protected);

        $manager->flush();
    }
}
