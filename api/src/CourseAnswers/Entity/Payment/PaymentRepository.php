<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Payment;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;

final class PaymentRepository
{
    private readonly EntityRepository $repo;

    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        $this->repo = $em->getRepository(Payment::class);
    }

    public function add(Payment $payment): void
    {
        $this->em->persist($payment);
    }

    public function get(PaymentId $paymentId): Payment
    {
        $payment = $this->repo->find($paymentId);
        if (null === $payment) {
            throw new DomainException('Payment not found.');
        }
        return $payment;
    }

    public function findByExternalId(string $externalId): ?Payment
    {
        return $this->repo->findOneBy(['externalId' => $externalId]);
    }
}
