<?php

declare(strict_types=1);

namespace App\Subscription\Entity\Subscription;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final readonly class SubscriptionRepository
{
    private EntityRepository $repo;

    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private EntityManagerInterface $em,
    ) {
        $this->repo = $em->getRepository(Subscription::class);
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
        $this->em->flush();
        return null;
    }
}
