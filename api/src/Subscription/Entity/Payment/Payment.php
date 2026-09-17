<?php

declare(strict_types=1);

namespace App\Subscription\Entity\Payment;

use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use App\Subscription\Entity\Subscription\Plan;

final class Payment implements AggregateRoot
{
    use EventTrait;

    public function __construct(
        private PaymentId $id,
        private string $externalId,
        private string $userId,
        private Plan $plan,
        private PaymentStatus $status,
        private Amount $amount,
        private int $durationDays,
    ) {}

    public function getId(): PaymentId
    {
        return $this->id;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getPlan(): Plan
    {
        return $this->plan;
    }

    public function getPaymentStatus(): PaymentStatus
    {
        return $this->status;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function getDurationDays(): int
    {
        return $this->durationDays;
    }
}
