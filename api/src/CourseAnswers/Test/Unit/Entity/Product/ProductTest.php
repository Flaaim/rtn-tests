<?php

declare(strict_types=1);

namespace App\CourseAnswers\Test\Unit\Entity\Product;

use App\CourseAnswers\Entity\Payment\Amount;
use App\CourseAnswers\Entity\Product\Product;
use App\CourseAnswers\Entity\Product\ProductId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class ProductTest extends TestCase
{
    public function testProduct(): void
    {
        $product = new Product(
            $id = ProductId::generate(),
            $cipher = 'ОТ 201.1',
            $courseId = '16ededf8-9cc1-42a2-a978-8d64024796f4',
            $amount = new Amount('100.00', 'RUB'),
            $filePath = 'test/file',
            $addedAt = new DateTimeImmutable('now'),
        );

        self::assertEquals($id, $product->getId());
        self::assertEquals($cipher, $product->getCipher());
        self::assertEquals($courseId, $product->getCourseId());
        self::assertEquals($amount, $product->getAmount());
        self::assertEquals($filePath, $product->getFilePath());
        self::assertEquals($addedAt, $product->getAddedAt());
    }

    public function testEditProduct(): void
    {
        $product = new Product(
            ProductId::generate(),
            'ОТ 201.1',
            '16ededf8-9cc1-42a2-a978-8d64024796f4',
            new Amount('100.00', 'RUB'),
            'test/file',
            new DateTimeImmutable('now'),
        );

        $product->edit(
            $cipher = 'ОТ 201.2',
            $amount = new Amount('105.00', 'RUB'),
            $filePath = 'test/file1',
            new DateTimeImmutable('now'),
        );

        self::assertEquals($cipher, $product->getCipher());
        self::assertEquals($amount, $product->getAmount());
        self::assertEquals($filePath, $product->getFilePath());
    }
}
