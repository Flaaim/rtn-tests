<?php

declare(strict_types=1);

namespace App\CourseAnswers\Test\Unit\Entity\Product;

use App\CourseAnswers\Entity\Product\Product;
use App\CourseAnswers\Entity\Product\ProductId;
use App\CourseAnswers\Entity\Product\Status;
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
            $paymentId = '1d71cd68-e737-4eb4-8395-5e8650fa3657',
            $status = Status::SEND,
            $sentAt = new DateTimeImmutable(),
        );

        self::assertEquals($id, $product->getId());
        self::assertEquals($cipher, $product->getCipher());
        self::assertEquals($paymentId, $product->getPaymentId());
        self::assertEquals($status, $product->getStatus());
        self::assertEquals($sentAt, $product->getSentAt());
    }
}
