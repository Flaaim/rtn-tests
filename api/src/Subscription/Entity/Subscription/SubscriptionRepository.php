<?php

declare(strict_types=1);

namespace App\Subscription\Entity\Subscription;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;

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
            ['userId' => $userId, 'status' => [Status::ACTIVE, Status::WAIT]],
            ['periodEnd' => 'DESC'],
        );

        foreach ($subscriptions as $subscription) {
            if ($subscription->isReadyToActivate()) {
                $subscription->activate();
                $this->em->flush();
            }

            if ($subscription->isActive()) {
                return $subscription;
            }

            if ($subscription->isWait()) {
                continue;
            }
            $subscription->expire();
        }
        $this->em->flush();
        return null;
    }

    public function get(SubscriptionId $id): Subscription
    {
        $subscription = $this->repo->find($id);
        if (null === $subscription) {
            throw new DomainException('Subscription not found.');
        }
        return $subscription;
    }

    public function remove(Subscription $subscription): void
    {
        $this->em->remove($subscription);
    }
}
