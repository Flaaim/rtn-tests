<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Test\GetBySlug;

use App\CourseAnswers\Entity\Payment\Amount;
use App\CourseAnswers\Entity\Product\ProductId;
use App\CourseAnswers\Test\Builder\ProductBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class ProductFixture extends AbstractFixture
{
    public const string PRODUCT_ID = 'b0cf090c-8f57-443b-ba21-0d88b94eaa1c';
    public const string PRODUCT_NAME = 'Оказание первой помощи пострадавщим';
    public const string PRODUCT_AMOUNT = '450.00';

    public function load(ObjectManager $manager): void
    {
        $product = new ProductBuilder()
            ->withProductId(new ProductId(self::PRODUCT_ID))
            ->withName(self::PRODUCT_NAME)
            ->withCourseId(CourseFixture::COURSE_ID)
            ->withAmount(new Amount(self::PRODUCT_AMOUNT))
            ->build();
        $manager->persist($product);

        $manager->flush();
    }
}
