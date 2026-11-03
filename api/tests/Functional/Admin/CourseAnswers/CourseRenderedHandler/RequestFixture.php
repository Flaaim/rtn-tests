<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\CourseAnswers\CourseRenderedHandler;

use App\CourseAnswers\Entity\Product\ProductId;
use App\CourseAnswers\Test\Builder\ProductBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string NEW_COURSE_ID = 'dbf22e66-f164-4d71-8f41-d1dffc2762dd';
    public const string NEW_CIPHER = 'ОТ 001.1';
    public const string NEW_FILE_PATH = 'file/test.pdf';

    public const string PRODUCT_ID = '88ba1327-1ef0-44e1-a885-2b0ac35093c7';
    public const string EXISTS_COURSE_ID = 'fc582967-e096-47b4-9d99-33482407bf8e';

    public function load(ObjectManager $manager): void
    {
        $product = new ProductBuilder()
            ->withProductId(new ProductId(self::PRODUCT_ID))
            ->withCourseId(self::EXISTS_COURSE_ID)
            ->withCipher('ОТ 201.2')
            ->withFilePath('test/file1')
            ->build();
        $manager->persist($product);

        $manager->flush();
    }
}
