<?php

declare(strict_types=1);

namespace App\Subscription\Command\Assign;

use App\Infrastructure\Doctrine\Flusher;
use App\Subscription\Entity\Subscription\Period;
use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Entity\Subscription\Status;
use App\Subscription\Entity\Subscription\Subscription;
use App\Subscription\Entity\Subscription\SubscriptionId;
use App\Subscription\Entity\Subscription\SubscriptionRepository;
use DateTimeImmutable;

final readonly class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private SubscriptionRepository $subscriptions,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $subscription = Subscription::create(
            SubscriptionId::generate(),
            $command->userId,
            Plan::from($command->plan),
            Status::ACTIVE,
            Period::create(
                new DateTimeImmutable($command->periodStart),
                new DateTimeImmutable($command->periodEnd)
            )
        );

        $this->subscriptions->add($subscription);

        $this->flusher->flush();
    }
}
