<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Entity;

use App\Testing\Entity\Status;
use App\Testing\Entity\Test;
use App\Testing\Entity\TestId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class TestTest extends TestCase
{
    public function testTest(): void
    {
        $test = new Test(
            $id = TestId::generate(),
            $name = 'Test name',
            $cipher = 'ОТ 201.18',
            $description = 'Test Description',
            $allowedMistakes = 3,
            'ba34f99c-6233-4be3-aa27-287d3726e54d',
            $tickets = [],
            $status = Status::inactive(),
            $slug = 'ot201',
            $createdAt = new DateTimeImmutable()
        );

        self::assertEquals($id, $test->getId());
        self::assertEquals($name, $test->getName());
        self::assertEquals($cipher, $test->getCipher());
        self::assertEquals($description, $test->getDescription());
        self::assertEquals($allowedMistakes, $test->getAllowedMistakes());
        self::assertEquals($tickets, $test->getTickets());
        self::assertEquals($status, $test->getStatus());
        self::assertEquals($slug, $test->getSlug());
        self::assertEquals($createdAt, $test->getCreatedAt());
    }
}
