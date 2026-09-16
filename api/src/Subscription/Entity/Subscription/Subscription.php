<?php

declare(strict_types=1);

namespace App\Subscription\Entity\Subscription;

use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use App\Subscription\Event\Subscription\SubscriptionPurchased;
use DateTimeImmutable;
use DomainException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'subscriptions')]
final class Subscription implements AggregateRoot
{
    use EventTrait;

    public function __construct(
        private SubscriptionId $id,
        private string $userId,
        private int $durationDays,
        private Plan $plan,
        private Status $status,
        private DateTimeImmutable $periodStart,
        private DateTimeImmutable $periodEnd
    ) {
        if ($this->plan->isTrial() && 1 !== $this->getDurationDays()) {
            throw new DomainException('Trial Subscription Period must be exactly 1 day.');
        }

        $this->recordEvent(new SubscriptionPurchased(
            $this->id->getValue(),
            $this->userId,
            $this->plan->value,
            $periodEnd->format('Y-m-d'),
        ));
    }

    public function getId(): SubscriptionId
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
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
