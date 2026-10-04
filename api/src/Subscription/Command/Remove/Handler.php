<?php

declare(strict_types=1);

namespace App\Subscription\Command\Remove;

use App\Infrastructure\Doctrine\Flusher;
use App\Subscription\Entity\Subscription\SubscriptionId;
use App\Subscription\Entity\Subscription\SubscriptionRepository;

final readonly class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private SubscriptionRepository $subscriptions,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $subscription = $this->subscriptions->get(new SubscriptionId($command->id));

        $this->subscriptions->remove($subscription);

        $this->flusher->flush();
    }
}
