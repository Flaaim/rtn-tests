<?php

declare(strict_types=1);

namespace Tests\Functional\CourseAnswers\Payment\Fail;

use App\CourseAnswers\Entity\Payment\Amount;
use App\CourseAnswers\Entity\Payment\Payment;
use App\CourseAnswers\Entity\Payment\PaymentId;
use App\CourseAnswers\Entity\Payment\PaymentStatus;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture implements DependentFixtureInterface
{
    public const string PAYMENT_ID = 'fd30af6b-3b26-4564-97be-228780f57807';
    public const string EMAIL = 'email@test.ru';
    public const string EXTERNAL_ID = 'f6ca311a-79e9-4195-aa6f-502761257361';
    public const string USER_ID = '9f1fc688-e4c7-40f3-b6b7-d0734bca2e5c';
    public const string EXTERNAL_ID_NOT_FOUND = '2a46b24d-b993-4bd6-a989-526459dc009a';

    public function load(ObjectManager $manager): void
    {
        $payment = new Payment(
            new PaymentId(self::PAYMENT_ID),
            self::EMAIL,
            ProductFixture::PRODUCT_ID,
            self::EXTERNAL_ID,
            PaymentStatus::PENDING,
            new Amount(ProductFixture::PRODUCT_AMOUNT),
        );
        $manager->persist($payment);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProductFixture::class,
        ];
    }
}
