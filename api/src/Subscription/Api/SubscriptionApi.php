<?php

declare(strict_types=1);

namespace App\Subscription\Api;

use App\Subscription\Command\Activate\Command;
use App\Subscription\Command\Activate\Handler;
use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Entity\Subscription\SubscriptionRepository;
use App\Subscription\Query\Subscription\SubscriptionFetcherInterface;
use DomainException;

/** @psalm-suppress UnusedClass */
final readonly class SubscriptionApi
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private SubscriptionFetcherInterface $subscriptions,
        private Handler $activateHandler,
        private SubscriptionRepository $subscriptionsRepo,
    ) {}

    public function ensureHasAccess(string $userId): void
    {
        if (null !== $this->subscriptionsRepo->findActiveByUserId($userId)) {
            return;
        }

        $latestSubscription = $this->subscriptions->getLatestByUserId($userId);

        if (empty($latestSubscription)) {
            $this->activateHandler->handle(new Command(
                userId: $userId,
                durationDays: 1,
                plan: Plan::TRIAL->value
            ));
            return;
        }

        if ($latestSubscription['plan'] === Plan::TRIAL->value) {
            throw new DomainException('Ваш пробный период завершен. Для продолжения необходимо приобрести подписку.');
        }

        throw new DomainException('Ваш оплаченный период завершен. Для продолжения необходимо приобрести подписку.');
    }
}
