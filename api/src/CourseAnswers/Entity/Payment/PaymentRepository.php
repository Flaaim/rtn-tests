<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Payment;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

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
}
