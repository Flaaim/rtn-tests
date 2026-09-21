<?php

declare(strict_types=1);

namespace App\Subscription\Entity\Subscription;

use DateTimeImmutable;
use DomainException;
use InvalidArgumentException;

final class Period
{
    private function __construct(
        private DateTimeImmutable $startDate,
        private DateTimeImmutable $endDate
    ) {
        $startDate = $startDate->setTime(0, 0);
        $endDate = $endDate->setTime(0, 0);

        if ($endDate < $startDate) {
            throw new DomainException('Subscription Period end date must be on or after start date.');
        }

        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public static function fromDurationDays(int $durationDays): self
    {
        if ($durationDays < 1) {
            throw new InvalidArgumentException('Subscription Period duration must be at least one day.');
        }
        $start = new DateTimeImmutable('today')->setTime(0, 0);
        $end = new DateTimeImmutable()->modify(\sprintf('+%d days', $durationDays));

        return new self($start, $end);
    }

    public static function create(DateTimeImmutable $startDate, DateTimeImmutable $endDate): self
    {
        return new self($startDate, $endDate);
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getDurationDays(): int
    {
        return (int)$this->startDate->diff($this->endDate)->days;
    }

    public function isActiveAt(DateTimeImmutable $moment): bool
    {
        $day = $moment->setTime(0, 0);

        return $day >= $this->startDate && $day <= $this->endDate;
    }

    public function extend(int $additionalDays): self
    {
        if ($additionalDays < 1) {
            throw new InvalidArgumentException('Subscription Period extension must be at least one day.');
        }

        $today = new DateTimeImmutable('today');

        if ($this->endDate < $today) {
            return new self(
                $today,
                $today->modify('+ ' . $additionalDays . ' days')
            );
        }

        return new self(
            $this->startDate,
            $this->endDate->modify('+ ' . $additionalDays . ' days'),
        );
    }
}
