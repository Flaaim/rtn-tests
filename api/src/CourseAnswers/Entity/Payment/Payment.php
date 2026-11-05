<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Payment;

use App\CourseAnswers\Event\Payment\PaymentConfirmed;
use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'product_payments')]
final class Payment implements AggregateRoot
{
    use EventTrait;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'confirmed_at', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $confirmedAt = null;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(name: 'payment_id', type: 'product_payment_id', unique: true)]
        private PaymentId $id,
        #[ORM\Column(name: 'email', type: 'string', length: 64)]
        private string $email,
        #[ORM\Column(name: 'product_id', type: 'string', length: 64)]
        private string $productId,
        #[ORM\Column(name: 'external_id', type: 'string', length: 64, unique: true)]
        private string $externalId,
        #[ORM\Column(type: 'string', length: 16, enumType: PaymentStatus::class)]
        private PaymentStatus $status,
        #[ORM\Embedded(class: Amount::class, columnPrefix: false)]
        private Amount $amount,
    ) {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): PaymentId
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
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

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getStatus(): PaymentStatus
    {
        return $this->status;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function confirm(): void
    {
        if (PaymentStatus::SUCCEEDED === $this->status) {
            return;
        }

        if (PaymentStatus::PENDING !== $this->status) {
            throw new DomainException('Payment has already been confirmed.');
        }
        $this->status = PaymentStatus::SUCCEEDED;
        $this->confirmedAt = new DateTimeImmutable();

        $this->recordEvent(new PaymentConfirmed(
            $this->email,
            $this->productId,
        ));
    }
}
