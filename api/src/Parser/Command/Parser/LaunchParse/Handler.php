<?php

declare(strict_types=1);

namespace App\Parser\Command\Parser\LaunchParse;

use App\Infrastructure\Doctrine\Flusher;
use App\Parser\Entity\Parser\ParserId;
use App\Parser\Entity\Task\Task;
use App\Parser\Entity\Task\TaskId;
use App\Parser\Entity\Task\TasksRepository;
use App\Parser\Query\Parser\HasOne\Query;
use App\Parser\Query\Parser\HasOne\QueryHandler;
use DateTimeImmutable;
use DomainException;

final class Handler
{
    public function __construct(
        private readonly Flusher $flusher,
        private readonly TasksRepository $tasks,
        private readonly QueryHandler $handler
    ) {}

    public function handle(Command $command): string
    {
        if (!$this->handler->handle(new Query($command->parserId))) {
            throw new DomainException('Parser not found.');
        }

        $task = new Task(
            TaskId::generate(),
            new ParserId($command->parserId),
            $command->branchId,
            $command->ticketId,
            new DateTimeImmutable()
        );

        $this->tasks->add($task);

        $this->flusher->flush();

        return $task->getId()->getValue();
    }
}
