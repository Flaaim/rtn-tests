<?php

declare(strict_types=1);

namespace App\CourseAnswers\Test\Unit\Entity\Payment\Command\Fail;

use App\CourseAnswers\Entity\Payment\Amount;
use App\CourseAnswers\Entity\Payment\Payment;
use App\CourseAnswers\Entity\Payment\PaymentId;
use App\CourseAnswers\Entity\Payment\PaymentStatus;
use DomainException;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class FailPaymentTest extends TestCase
{
    public function testSucceeded(): void
    {
        $payment = new Payment(
            PaymentId::generate(),
            'test@email.ru',
            '3b96a7d0-88a4-43cd-aee2-88b4fa81b7ef',
            'e4c3b83f-4cee-4e1f-b5d8-38b11101b723',
            PaymentStatus::SUCCEEDED,
            new Amount('350.00')
        );

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Only pending payment can be failed.');
        $payment->fail();
    }

    #[CoversNothing]
    public function testFailedAlready(): void
    {
        $payment = new Payment(
            PaymentId::generate(),
            'test@email.ru',
            '3b96a7d0-88a4-43cd-aee2-88b4fa81b7ef',
            'e4c3b83f-4cee-4e1f-b5d8-38b11101b723',
            PaymentStatus::FAILED,
            new Amount('350.00')
        );

        $payment->fail();
    }

    public function testSuccess(): void
    {
        $payment = new Payment(
            PaymentId::generate(),
            'test@email.ru',
            '3b96a7d0-88a4-43cd-aee2-88b4fa81b7ef',
            'e4c3b83f-4cee-4e1f-b5d8-38b11101b723',
            PaymentStatus::PENDING,
            new Amount('350.00')
        );

        $payment->fail();

        self::assertEquals(PaymentStatus::FAILED, $payment->getStatus());
    }
}
