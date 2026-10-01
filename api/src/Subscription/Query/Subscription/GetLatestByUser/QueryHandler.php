<?php

declare(strict_types=1);

namespace App\Subscription\Query\Subscription\GetLatestByUser;

use App\Subscription\Query\Subscription\SubscriptionFetcherInterface;
use DomainException;

final readonly class QueryHandler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private SubscriptionFetcherInterface $subscriptions
    ) {}

    public function handle(Query $query): SubscriptionDTO
    {
        $userId = $query->userId;
        $subscription = $this->subscriptions->getLatestByUserId($userId);

        if (empty($subscription)) {
            throw new DomainException('Подписка не найдена.');
        }

        return SubscriptionDTO::fromArray($subscription);
    }
}
