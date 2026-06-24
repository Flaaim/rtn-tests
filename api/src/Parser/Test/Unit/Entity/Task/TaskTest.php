<?php

declare(strict_types=1);

namespace App\Parser\Test\Unit\Entity\Task;

use App\Parser\Entity\Task\Status;
use App\Parser\Entity\Task\Task;
use App\Parser\Entity\Task\TaskId;
use PHPUnit\Framework\TestCase;

final class TaskTest extends TestCase
{
    public function testSuccess(): void
    {
        $task = new Task(
            $id = TaskId::generate(),
        );

        self::assertEquals($id, $task->getId());
        self::assertEquals(Status::PROCESSING, $task->getStatus()->getValue());
        self::assertNull($task->getResult());
    }
}
