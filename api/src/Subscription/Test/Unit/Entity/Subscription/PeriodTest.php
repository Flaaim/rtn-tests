<?php

declare(strict_types=1);

namespace App\Subscription\Test\Unit\Entity\Subscription;

use App\Subscription\Entity\Subscription\Period;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class PeriodTest extends TestCase
{
    public function testPeriod(): void
    {
        $period = Period::create(
            $start = new DateTimeImmutable('now'),
            $end = new DateTimeImmutable('+ 4 days'),
        );

        self::assertEquals($start->format('Y-m-d'), $period->getStartDate()->format('Y-m-d'));
        self::assertEquals($end->format('Y-m-d'), $period->getEndDate()->format('Y-m-d'));
        self::assertEquals(4, $period->getDurationDays());
    }

    public function testFromDurationDays(): void
    {
        $period = Period::fromDurationDays(5);

        self::assertEquals(5, $period->getDurationDays());
    }

    public function testFromDurationDaysLessTheOne(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Subscription Period duration must be at least one day.');
        Period::fromDurationDays(0);
    }

    public function testExtendPeriod(): void
    {
        $period = Period::create(
            new DateTimeImmutable('now'),
            new DateTimeImmutable('4 days'),
        );

        $newPeriod = $period->extend(2);

        self::assertEquals(6, $newPeriod->getDurationDays());
    }

    public function testExtendPeriodLessThenOne(): void
    {
        $period = Period::create(
            new DateTimeImmutable('21.09.2026'),
            new DateTimeImmutable('25.09.2026'),
        );

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Subscription Period extension must be at least one day.');
        $period->extend(0);
    }
}
