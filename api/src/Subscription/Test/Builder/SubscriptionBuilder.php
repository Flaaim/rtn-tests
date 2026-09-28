<?php

declare(strict_types=1);

namespace App\Subscription\Test\Builder;

use App\Subscription\Entity\Subscription\Period;
use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Entity\Subscription\Status;
use App\Subscription\Entity\Subscription\Subscription;
use App\Subscription\Entity\Subscription\SubscriptionId;
use DateTimeImmutable;
use ReflectionClass;

final class SubscriptionBuilder
{
    private SubscriptionId $id;
    private string $userId;
    private Plan $plan;
    private Status $status;
    private DateTimeImmutable $periodStart;
    private DateTimeImmutable $periodEnd;
    /** @psalm-suppress UnusedProperty */
    private int $durationDays;
    private bool $isTrialUsed = false;

    /** @psalm-suppress PossiblyUnusedMethod  */
    public function __construct(
    ) {
        $this->id = new SubscriptionId('09ace734-919c-4b9b-a699-aff2d27da111');
        $this->userId = '4664ecc0-ccb0-4984-84fe-377f2a12b4cc';
        $this->plan = Plan::BASIC;
        $this->status = Status::ACTIVE;
        $this->periodStart = new DateTimeImmutable('now');
        $this->periodEnd = new DateTimeImmutable('+ 5 days');
        $this->durationDays = 5;
    }

    /** @psalm-suppress PossiblyUnusedMethod  */
    public function withId(SubscriptionId $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod  */
    public function withUserId(string $userId): self
    {
        $clone = clone $this;
        $clone->userId = $userId;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod  */
    public function withTrialPlan(): self
    {
        $clone = clone $this;
        $clone->plan = Plan::TRIAL;
        $clone->periodStart = new DateTimeImmutable('now');
        $clone->periodEnd = new DateTimeImmutable('+ 1 day');
        $clone->isTrialUsed = true;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod  */
    public function withBasicPlan(?Period $period = null): self
    {
        $clone = clone $this;
        $clone->plan = Plan::BASIC;
        $clone->status = Status::ACTIVE;
        $clone->periodStart = (null !== $period) ? $period->getStartDate() : new DateTimeImmutable('now');
        $clone->periodEnd = (null !== $period) ? $period->getEndDate() : new DateTimeImmutable('+ 5 day');
        $clone->isTrialUsed = true;

        if (null !== $period) {
            $clone->durationDays = $clone->periodStart->diff($clone->periodEnd)->days;
        }

        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod  */
    public function withExpiredPlan(): self
    {
        $clone = clone $this;
        $clone->plan = Plan::BASIC;
        $clone->status = Status::EXPIRED;
        $clone->periodStart = new DateTimeImmutable('- 10 days');
        $clone->periodEnd = new DateTimeImmutable('- 5 days');
        $clone->isTrialUsed = true;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod  */
    public function withStaleActivePlan(): self
    {
        $clone = clone $this;
        $clone->plan = Plan::BASIC;
        $clone->status = Status::ACTIVE;
        $clone->periodStart = new DateTimeImmutable('- 10 days');
        $clone->periodEnd = new DateTimeImmutable('- 5 days');
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod  */
    public function withTrialUsed(): self
    {
        $clone = clone $this;
        $clone->plan = Plan::TRIAL;
        $clone->status = Status::EXPIRED;
        $clone->periodStart = new DateTimeImmutable('- 10 days');
        $clone->periodEnd = new DateTimeImmutable('- 9 days');
        $clone->isTrialUsed = true;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod  */
    public function build(): Subscription
    {
        $reflection = new ReflectionClass(Subscription::class);
        $constructor = $reflection->getConstructor();
        $subscription = $reflection->newInstanceWithoutConstructor();

        /** @psalm-suppress PossiblyNullReference */
        $constructor->invoke(
            $subscription,
            $this->id,
            $this->userId,
            $this->plan,
            $this->status,
            Period::create($this->periodStart, $this->periodEnd)
        );
        if ($reflection->hasProperty('isTrialUsed')) {
            $prop = $reflection->getProperty('isTrialUsed');
            $prop->setValue($subscription, $this->isTrialUsed);
        }

        return $subscription;
    }
}
