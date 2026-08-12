<?php

declare(strict_types=1);

namespace App\Testing\Entity\DTO;

final class TicketDTO
{
    public function __construct(
        public int $number,
        public array $questionIds,
    ) {}
}
