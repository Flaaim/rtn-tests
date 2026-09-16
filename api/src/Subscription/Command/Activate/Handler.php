<?php

declare(strict_types=1);

namespace App\Subscription\Command\Activate;

use App\Infrastructure\Doctrine\Flusher;
use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Entity\Subscription\Status;
use App\Subscription\Entity\Subscription\Subscription;
use App\Subscription\Entity\Subscription\SubscriptionId;
use App\Subscription\Entity\Subscription\SubscriptionRepository;
use DateInterval;
use DateTimeImmutable;
use DomainException;

final readonly class Handler
{
    public function __construct(
        private SubscriptionRepository $subscriptions,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        if ($this->subscriptions->hasActiveByUserId($command->userId)) {
            throw new DomainException('User already has an active subscription.');
        }

        $subscription = new Subscription(
            SubscriptionId::generate(),
            $command->userId,
            $command->durationDays,
            Plan::from($command->plan),
            Status::from('active'),
            new DateTimeImmutable(),
            new DateTimeImmutable()->add(new DateInterval('P' . $command->durationDays . 'D')),
        );

        $this->subscriptions->add($subscription);

        $this->flusher->flush();
    }
}
