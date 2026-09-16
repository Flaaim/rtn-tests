<?php

declare(strict_types=1);

namespace App\Subscription\Entity\Subscription;

use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use DateTimeImmutable;

final class Subscription implements AggregateRoot
{
    use EventTrait;

    public function __construct(
        private SubscriptionId $id,
        private string $userId,
        private string $testId,
        private int $durationDays,
        private Plan $plan,
        private Status $status,
        private DateTimeImmutable $periodStart,
        private DateTimeImmutable $periodEnd,
    ) {}

    public function getId(): SubscriptionId
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getTestId(): string
    {
        return $this->testId;
    }

    public function getDurationDays(): int
    {
        return $this->durationDays;
    }

    public function getPlan(): Plan
    {
        return $this->plan;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getPeriodStart(): DateTimeImmutable
    {
        return $this->periodStart;
    }

    public function getPeriodEnd(): DateTimeImmutable
    {
        return $this->periodEnd;
    }
}
