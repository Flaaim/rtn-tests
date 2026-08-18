<?php

declare(strict_types=1);

namespace App\Testing\Entity\Test;

use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final class Settings
{
    public function __construct(
        #[ORM\Column(type: 'integer')]
        private int $numberOfTickets,
        #[ORM\Column(type: 'integer')]
        private int $numberQuestionsInTicket,
        #[ORM\Column(type: 'integer')]
        private int $allowedMistakes,
    ) {
        Assert::greaterThanEq($numberOfTickets, 1, 'Number of tickets must be at least 1.');
        Assert::greaterThanEq($numberQuestionsInTicket, 1, 'Questions in ticket must be at least 1.');
        Assert::greaterThanEq($allowedMistakes, 0, 'Allowed mistakes cannot be negative.');

        if ($allowedMistakes >= $numberQuestionsInTicket) {
            throw new DomainException('Allowed mistakes cannot be greater than or equal to questions in a ticket.');
        }
    }

    public function getNumberOfTickets(): int
    {
        return $this->numberOfTickets;
    }

    public function getNumberQuestionsInTicket(): int
    {
        return $this->numberQuestionsInTicket;
    }

    public function getAllowedMistakes(): int
    {
        return $this->allowedMistakes;
    }
}
