<?php

declare(strict_types=1);

namespace App\Parser\Test\Unit\Entity\Task;

use App\Parser\Entity\Parser\ParserId;
use App\Parser\Entity\Task\Status;
use App\Parser\Entity\Task\Task;
use App\Parser\Entity\Task\TaskId;
use App\Parser\Event\ParseEnded;
use App\Parser\Event\ParseFailed;
use App\Parser\Event\ParseLaunched;
use PHPUnit\Framework\TestCase;

final class TaskTest extends TestCase
{
    public function testCreate(): void
    {
        $task = new Task(
            $taskId = TaskId::generate(),
            $parserId = ParserId::generate(),
            $branchId = 'branchId',
            $ticketId = 'ticketId',
        );

        self::assertEquals($taskId, $task->getId());
        self::assertEquals($parserId, $task->getParserId());
        self::assertEquals($branchId, $task->getBranchId());
        self::assertEquals($ticketId, $task->getTicketId());

        self::assertEquals(Status::PROCESSING, $task->getStatus()->getValue());
        self::assertNull($task->getDraft());

        $events = $task->releaseEvents();

        $event = end($events);

        self::assertInstanceOf(ParseLaunched::class, $event);
        self::assertEquals($event->taskId, $task->getId()->getValue());
        self::assertEquals($event->parserId, $task->getParserId()->getValue());
        self::assertEquals($event->branchId, $task->getBranchId());
        self::assertEquals($event->ticketId, $task->getTicketId());
    }

    public function testFailed(): void
    {
        $task = new Task(
            TaskId::generate(),
            ParserId::generate(),
            'branchId',
            'ticketId',
        );

        $task->failed($reason = 'Ошибка сети.');
        self::assertEquals(Status::FAILED, $task->getStatus()->getValue());
        self::assertNull($task->getDraft());
        self::assertEquals($reason, $task->getFailedReason());

        $events = $task->releaseEvents();
        $event = end($events);

        self::assertInstanceOf(ParseFailed::class, $event);
        self::assertEquals($event->parseId, $task->getParserId()->getValue());
        self::assertEquals($event->reason, $task->getFailedReason());
    }

    public function testFailedAlready(): void
    {
        $task = new Task(
            TaskId::generate(),
            ParserId::generate(),
            'branchId',
            'ticketId',
        );

        $task->failed('Ошибка сети.');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Task already failed.');
        $task->failed('Ошибка');
    }

    public function testEnded(): void
    {
        $task = new Task(
            TaskId::generate(),
            ParserId::generate(),
            'branchId',
            'ticketId',
        );

        $task->ended($draft = 'draft');

        self::assertEquals(Status::COMPLETED, $task->getStatus()->getValue());
        self::assertNull($task->getFailedReason());
        self::assertEquals($draft, $task->getDraft());

        $events = $task->releaseEvents();

        $event = end($events);

        self::assertInstanceOf(ParseEnded::class, $event);

        self::assertEquals($event->taskId, $task->getId()->getValue());
        self::assertEquals($event->parserId, $task->getParserId()->getValue());
    }

    public function testEndedAlready(): void
    {
        $task = new Task(
            TaskId::generate(),
            ParserId::generate(),
            'branchId',
            'ticketId',
        );

        $task->ended($draft = 'draft');

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Task is already ended.');
        $task->ended('draft');
    }

}
