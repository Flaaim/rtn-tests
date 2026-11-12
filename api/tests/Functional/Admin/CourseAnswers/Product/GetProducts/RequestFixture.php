<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\CourseAnswers\Product\GetProducts;

use App\CourseAnswers\Entity\Payment\Amount;
use App\CourseAnswers\Entity\Product\ProductId;
use App\CourseAnswers\Test\Builder\ProductBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string PRODUCT_ID = 'b0cf090c-8f57-443b-ba21-0d88b94eaa1c';
    public const string COURSE_ID = 'd81b39f0-321c-40fa-9bbc-88b444839d93';
    public const string PRODUCT_CIPHER = 'ОТ 201.1';
    public const string PRODUCT_FILEPATH = 'file/test.pdf';
    public const string PRODUCT_AMOUNT = '450.00';

    public function load(ObjectManager $manager): void
    {
        $product = new ProductBuilder()
            ->withProductId(new ProductId(self::PRODUCT_ID))
            ->withCourseId(self::COURSE_ID)
            ->withFilePath(self::PRODUCT_FILEPATH)
            ->withAmount(new Amount(self::PRODUCT_AMOUNT))
            ->withCipher(self::PRODUCT_CIPHER)
            ->build();
        $manager->persist($product);

        $manager->flush();
    }
}
