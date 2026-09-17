<?php

declare(strict_types=1);

namespace App\Subscription\Entity\Payment;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class PaymentRepository
{
    /** @psalm-suppress UnusedProperty */
    private readonly EntityRepository $repo;

    /** @psalm-suppress PossiblyUnusedMethod */
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
