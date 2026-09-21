<?php

declare(strict_types=1);

namespace App\Subscription\Query\GetSubscription;

use App\Subscription\Query\SubscriptionFetcherInterface;

final readonly class QueryHandler
{
    public function __construct(
        private SubscriptionFetcherInterface $subscriptions
    ) {}

    public function handle(Query $query): SubscriptionDTO
    {
        $userId = $query->userId;
        $subscription = $this->subscriptions->findActiveByUserId($userId);

        if (empty($subscription)) {
            throw new \DomainException('Подписка не найдена.');
        }

        return SubscriptionDTO::fromArray($subscription);
    }
}
