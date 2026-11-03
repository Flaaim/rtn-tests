<?php

declare(strict_types=1);

namespace App\CourseAnswers\Test\Unit\Entity\Payment;

use App\CourseAnswers\Entity\Payment\Amount;
use App\CourseAnswers\Entity\Payment\Payment;
use App\CourseAnswers\Entity\Payment\PaymentId;
use App\CourseAnswers\Entity\Payment\PaymentStatus;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class PaymentTest extends TestCase
{
    public function testPayment(): void
    {
        $payment = new Payment(
            $id = PaymentId::generate(),
            $email = 'email@test.ru',
            $productId = 'ec9d23c1-bacf-45aa-a597-64edcb714b0e',
            $externalId = '3b96a7d0-88a4-43cd-aee2-88b4fa81b7ef',
            $status = PaymentStatus::PENDING,
            $amount = new Amount('100.00'),
        );

        self::assertEquals($id, $payment->getId());
        self::assertEquals($email, $payment->getEmail());
        self::assertEquals($productId, $payment->getProductId());
        self::assertEquals($externalId, $payment->getExternalId());
        self::assertEquals($status, $payment->getStatus());
        self::assertEquals($amount, $payment->getAmount());
    }
}
