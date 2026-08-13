<?php

declare(strict_types=1);

namespace App\Testing\Entity\Test\DTO;

use JsonSerializable;

final class TicketDTO implements JsonSerializable
{
    public function __construct(
        public int $number,
        public array $questionIds,
    ) {}

    public static function fromArray(array $ticketData): self
    {
        return new self(
            number: $ticketData['number'],
            questionIds: $ticketData['questionIds'],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'number' => $this->number,
            'questionIds' => $this->questionIds,
        ];
    }
}
