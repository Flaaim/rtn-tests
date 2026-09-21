<?php

declare(strict_types=1);

namespace App\Subscription\Entity\Payment;

use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Event\Payment\PaymentConfirmed;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'payments')]
final class Payment implements AggregateRoot
{
    use EventTrait;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'confirmed_at', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $confirmedAt = null;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'payment_id')]
        private PaymentId $id,
        #[ORM\Column(name: 'external_id', type: 'string', length: 64, unique: true)]
        private string $externalId,
        #[ORM\Column(type: 'string')]
        private string $userId,
        #[ORM\Column(type: 'string', length: 16, enumType: Plan::class)]
        private Plan $plan,
        #[ORM\Column(type: 'string', length: 16, enumType: PaymentStatus::class)]
        private PaymentStatus $status,
        #[ORM\Embedded(class: Amount::class, columnPrefix: false)]
        private Amount $amount,
        #[ORM\Column(type: 'integer')]
        private int $durationDays,
    ) {
        $this->createdAt = new DateTimeImmutable();
    }

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

    /** @psalm-suppress PossiblyUnusedMethod */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function getConfirmedAt(): ?DateTimeImmutable
    {
        return $this->confirmedAt;
    }

    public function confirm(): void
    {
        if (PaymentStatus::SUCCEEDED === $this->status) {
            return;
        }

        if (PaymentStatus::PENDING !== $this->status) {
            throw new DomainException('Only pending payment can be confirmed.');
        }

        $this->status = PaymentStatus::SUCCEEDED;
        $this->confirmedAt = new DateTimeImmutable();

        $this->recordEvent(
            new PaymentConfirmed(
                $this->id->getValue(),
                $this->userId,
                $this->plan->value,
                $this->durationDays,
            )
        );
    }
}
