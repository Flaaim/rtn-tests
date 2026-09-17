<?php

declare(strict_types=1);

namespace App\Subscription\Api;

use App\Subscription\Command\Activate\Command;
use App\Subscription\Command\Activate\Handler;
use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Entity\Subscription\SubscriptionRepository;
use DomainException;

/** @psalm-suppress UnusedClass */
final readonly class SubscriptionApi
{
    public function __construct(
        private SubscriptionRepository $subscriptions,
        private Handler $activateHandler
    ) {}

    public function ensureHasAccess(string $userId): void
    {
        if ($this->subscriptions->hasActiveByUserId($userId)) {
            return;
        }

        if ($this->subscriptions->hasTrialByUserId($userId)) {
            throw new DomainException('Ваш пробный период завершен. Для продолжения необходимо приобрести подписку.');
        }

        $this->activateHandler->handle(new Command(
            userId: $userId,
            durationDays: 1,
            plan: Plan::TRIAL->value
        ));
    }
}
