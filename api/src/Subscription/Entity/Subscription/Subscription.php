<?php

declare(strict_types=1);

namespace App\Subscription\Entity\Subscription;

use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use App\Subscription\Event\Subscription\SubscriptionExpired;
use App\Subscription\Event\Subscription\SubscriptionPurchased;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'subscriptions')]
final class Subscription implements AggregateRoot
{
    use EventTrait;
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $periodStart;
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $periodEnd;
    #[ORM\Column(type: 'integer')]
    private int $durationDays;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isTrialUsed = false;
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'subscription_id', unique: true)]
        private SubscriptionId $id,
        #[ORM\Column(type: 'string')]
        private string $userId,
        #[ORM\Column(type: 'string', length: 16, enumType: Plan::class)]
        private Plan $plan,
        #[ORM\Column(type: 'string', length: 16, enumType: Status::class)]
        private Status $status,
        Period $period
    ) {
        $this->periodStart = $period->getStartDate();
        $this->periodEnd = $period->getEndDate();
        $this->durationDays = $period->getDurationDays();

        if ($this->plan->isTrial() && 1 !== $this->getDurationDays()) {
            throw new DomainException('Trial Subscription Period must be exactly 1 day.');
        }

        if($this->plan->isTrial()){
            $this->isTrialUsed = true;
        }

        $this->recordEvent(new SubscriptionPurchased(
            $this->id->getValue(),
            $this->userId,
            $this->plan->value,
            $this->periodEnd->format('Y-m-d'),
        ));
    }

    public function getId(): SubscriptionId
    {
        return $this->id;
    }

    /** @psalm-suppress PossiblyUnusedMethod  */
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
    public function isTrialUsed(): bool
    {
        return $this->isTrialUsed;
    }
    public function getPeriodStart(): DateTimeImmutable
    {
        return $this->periodStart;
    }

    public function getPeriodEnd(): DateTimeImmutable
    {
        return $this->periodEnd;
    }

    public function getPeriod(): Period
    {
        return Period::create($this->periodStart, $this->periodEnd);
    }

    public function isActive(): bool
    {
        if (Status::ACTIVE !== $this->status) {
            return false;
        }

        return $this->getPeriod()->isActiveAt(new DateTimeImmutable('today'));
    }

    public function extend(int $additionalDays): void
    {
        if (Status::CANCELLED === $this->status) {
            throw new DomainException('Cancelled User Subscription cannot be extended.');
        }
        $extendedPeriod = $this->getPeriod()->extend($additionalDays);

        $this->periodEnd = $extendedPeriod->getEndDate();
        $this->durationDays = $extendedPeriod->getDurationDays();

        if (Status::EXPIRED === $this->status && $this->isActive()) {
            $this->status = Status::ACTIVE;
        }
    }

    public function expire(): void
    {
        if (Status::EXPIRED === $this->status) {
            return;
        }

        if (Status::CANCELLED === $this->status) {
            throw new DomainException('Cancelled User Subscription cannot expire.');
        }

        $this->status = Status::EXPIRED;

        $this->recordEvent(new SubscriptionExpired($this->userId));
    }
}
