<?php

declare(strict_types=1);

namespace App\Event\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Entity]
#[ORM\Table(name: 'event_store_events')]
final class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;
    #[ORM\Column(type: 'string', length: 255)]
    private string $type;
    #[ORM\Column(type: 'json')]
    private array $payload;
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(string $type, array $payload)
    {
        Assert::notEmpty($type);
        $this->type = $type;
        $this->payload = $payload;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id ?? 0;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
