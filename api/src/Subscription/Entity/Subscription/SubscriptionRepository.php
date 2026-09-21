<?php

declare(strict_types=1);

namespace App\Subscription\Entity\Subscription;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class SubscriptionRepository
{
    private readonly EntityRepository $repo;

    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        $this->repo = $em->getRepository(Subscription::class);
    }

    public function hasActiveByUserId(string $userId): bool
    {
        return (bool)$this->repo->createQueryBuilder('t')
            ->select('1')
            ->andWhere('t.userId = :userId')
            ->andWhere('t.status = :status')
            ->setParameter('userId', $userId)
            ->setParameter('status', Status::ACTIVE)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function hasTrialByUserId(string $userId): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.userId = :userId')
            ->andWhere('t.plan = :plan')
            ->setParameter('userId', $userId)
            ->setParameter('plan', Plan::TRIAL->value)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function add(Subscription $subscription): void
    {
        $this->em->persist($subscription);
    }

    public function findActiveByUserId(string $userId): ?Subscription
    {
        /** @var Subscription[] $subscriptions */
        $subscriptions = $this->repo->findBy(
            ['userId' => $userId, 'status' => Status::ACTIVE],
            ['periodEnd' => 'DESC'],
        );

        foreach ($subscriptions as $subscription) {
            if ($subscription->isActive()) {
                return $subscription;
            }

            $subscription->expire();
        }

        return null;
    }
}
