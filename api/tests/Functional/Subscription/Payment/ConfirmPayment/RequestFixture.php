<?php

declare(strict_types=1);

namespace Tests\Functional\Subscription\Payment\ConfirmPayment;

use App\Subscription\Entity\Payment\Amount;
use App\Subscription\Entity\Payment\Payment;
use App\Subscription\Entity\Payment\PaymentId;
use App\Subscription\Entity\Payment\PaymentStatus;
use App\Subscription\Entity\Subscription\Plan;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string PAYMENT_ID = 'fd30af6b-3b26-4564-97be-228780f57807';
    public const string EXTERNAL_ID = 'f6ca311a-79e9-4195-aa6f-502761257361';
    public const string USER_ID = '9f1fc688-e4c7-40f3-b6b7-d0734bca2e5c';
    public const string EXTERNAL_ID_NOT_FOUND = '2a46b24d-b993-4bd6-a989-526459dc009a';

    public function load(ObjectManager $manager): void
    {
        $payment = new Payment(
            new PaymentId(self::PAYMENT_ID),
            self::EXTERNAL_ID,
            self::USER_ID,
            Plan::BASIC,
            PaymentStatus::PENDING,
            new Amount('100.00'),
            10,
        );
        $manager->persist($payment);

        $manager->flush();
    }
}
