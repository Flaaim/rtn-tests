<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Course\Course\Get;

use App\CourseAnswers\Entity\Payment\Amount;
use App\CourseAnswers\Entity\Product\ProductId;
use App\CourseAnswers\Test\Builder\ProductBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class ProductFixture extends AbstractFixture
{
    public const string PRODUCT_ID = 'b0cf090c-8f57-443b-ba21-0d88b94eaa1c';
    public const string PRODUCT_CIPHER = 'ОТ 201.1';
    public const string PRODUCT_FILEPATH = 'file/test.pdf';
    public const string PRODUCT_AMOUNT = '450.00';

    public function load(ObjectManager $manager): void
    {
        $product = new ProductBuilder()
            ->withProductId(new ProductId(self::PRODUCT_ID))
            ->withCourseId(RequestFixture::COURSE_ID)
            ->withFilePath(self::PRODUCT_FILEPATH)
            ->withAmount(new Amount(self::PRODUCT_AMOUNT))
            ->withCipher(self::PRODUCT_CIPHER)
            ->build();
        $manager->persist($product);

        $manager->flush();
    }
}
