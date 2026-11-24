<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\CourseAnswers\Product\GetAllByCourseIds;

use App\CourseAnswers\Entity\Payment\Amount;
use App\CourseAnswers\Entity\Product\ProductId;
use App\CourseAnswers\Test\Builder\ProductBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string PRODUCT_ID_ONE = 'b0cf090c-8f57-443b-ba21-0d88b94eaa1c';
    public const string PRODUCT_ID_TWO = 'b9be2492-8746-4588-bed7-a5e41e463055';
    public const array COURSE_IDS = ['d81b39f0-321c-40fa-9bbc-88b444839d93', 'f3831b9c-b67a-4538-869e-499cedf8d4cb'];

    public const string PRODUCT_CIPHER = 'ОТ 201.1';
    public const string PRODUCT_FILEPATH = 'file/test.pdf';
    public const string PRODUCT_AMOUNT = '450.00';

    public function load(ObjectManager $manager): void
    {
        $oneProduct = new ProductBuilder()
            ->withProductId(new ProductId(self::PRODUCT_ID_ONE))
            ->withCourseId(self::COURSE_IDS[0])
            ->withFilePath(self::PRODUCT_FILEPATH)
            ->withAmount(new Amount(self::PRODUCT_AMOUNT))
            ->withCipher(self::PRODUCT_CIPHER)
            ->build();
        $manager->persist($oneProduct);

        $secondProduct = new ProductBuilder()
            ->withProductId(new ProductId(self::PRODUCT_ID_TWO))
            ->withCourseId(self::COURSE_IDS[1])
            ->withFilePath(self::PRODUCT_FILEPATH)
            ->withAmount(new Amount(self::PRODUCT_AMOUNT))
            ->withCipher(self::PRODUCT_CIPHER)
            ->build();
        $manager->persist($secondProduct);

        $manager->flush();
    }
}
