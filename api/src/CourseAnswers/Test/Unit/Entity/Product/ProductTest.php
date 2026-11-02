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
    public function testFile(): void
    {
        $product = new Product(
            $id = ProductId::generate(),
            $cipher = 'ОТ 201.1',
            $amount = new Amount('100.00', 'RUB'),
            $filePath = 'test/file',
            $addedAt = new DateTimeImmutable('now'),
        );

        self::assertEquals($id, $product->getId());
        self::assertEquals($cipher, $product->getCipher());
        self::assertEquals($amount, $product->getAmount());
        self::assertEquals($filePath, $product->getFilePath());
        self::assertEquals($addedAt, $product->getAddedAt());
    }
}
