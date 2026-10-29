<?php

declare(strict_types=1);

namespace App\CourseAnswers\Test\Unit\Entity\Payment\Command\Confirm;

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
final class ConfirmPaymentTest extends TestCase
{
    #[CoversNothing]
    public function testConfirmSucceeded(): void
    {
        $payment = new Payment(
            PaymentId::generate(),
            'test@email.ru',
            '3b96a7d0-88a4-43cd-aee2-88b4fa81b7ef',
            'e4c3b83f-4cee-4e1f-b5d8-38b11101b723',
            PaymentStatus::SUCCEEDED,
            new Amount('350.00')
        );

        $payment->confirm();
    }

    public function testConfirmFailed(): void
    {
        $payment = new Payment(
            PaymentId::generate(),
            'test@email.ru',
            '3b96a7d0-88a4-43cd-aee2-88b4fa81b7ef',
            'e4c3b83f-4cee-4e1f-b5d8-38b11101b723',
            PaymentStatus::FAILED,
            new Amount('350.00')
        );

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Payment has already been confirmed.');
        $payment->confirm();
    }

    public function testConfirmSucceed(): void
    {
        $payment = new Payment(
            PaymentId::generate(),
            'test@email.ru',
            '3b96a7d0-88a4-43cd-aee2-88b4fa81b7ef',
            'e4c3b83f-4cee-4e1f-b5d8-38b11101b723',
            PaymentStatus::PENDING,
            new Amount('350.00')
        );

        $payment->confirm();

        self::assertEquals(PaymentStatus::SUCCEEDED, $payment->getStatus());
    }
}
