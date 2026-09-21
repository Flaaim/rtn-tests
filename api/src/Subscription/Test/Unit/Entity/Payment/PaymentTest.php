<?php

declare(strict_types=1);

namespace App\Subscription\Test\Unit\Entity\Payment;

use App\Subscription\Entity\Payment\Amount;
use App\Subscription\Entity\Payment\Payment;
use App\Subscription\Entity\Payment\PaymentId;
use App\Subscription\Entity\Payment\PaymentStatus;
use App\Subscription\Entity\Subscription\Plan;
use DomainException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 * @coversNothing
 */
final class PaymentTest extends TestCase
{
    public function testCreate(): void
    {
        $payment = new Payment(
            $id = PaymentId::generate(),
            $externalId = Uuid::uuid4()->toString(),
            $userId = Uuid::uuid4()->toString(),
            $plan = Plan::BASIC,
            $status = PaymentStatus::PENDING,
            $amount = new Amount('100.00'),
            $durationDays = 10,
        );

        self::assertEquals($id, $payment->getId());
        self::assertEquals($externalId, $payment->getExternalId());
        self::assertEquals($userId, $payment->getUserId());
        self::assertEquals($plan, $payment->getPlan());
        self::assertEquals($status, $payment->getPaymentStatus());
        self::assertEquals($amount, $payment->getAmount());
        self::assertEquals($durationDays, $payment->getDurationDays());
    }

    public function testConfirmPending(): void
    {
        $payment = new Payment(
            PaymentId::generate(),
            Uuid::uuid4()->toString(),
            Uuid::uuid4()->toString(),
            Plan::BASIC,
            PaymentStatus::PENDING,
            new Amount('100.00'),
            10,
        );

        $payment->confirm();
        self::assertEquals(PaymentStatus::SUCCEEDED, $payment->getPaymentStatus());
    }

    public function testConfirmNotPending(): void
    {
        $payment = new Payment(
            PaymentId::generate(),
            Uuid::uuid4()->toString(),
            Uuid::uuid4()->toString(),
            Plan::BASIC,
            PaymentStatus::FAILED,
            new Amount('100.00'),
            10,
        );

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Only pending payment can be confirmed.');
        $payment->confirm();
    }
}
