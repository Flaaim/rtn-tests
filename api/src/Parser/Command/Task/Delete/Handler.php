<?php

declare(strict_types=1);

namespace App\Parser\Command\Task\Delete;

use App\Infrastructure\Doctrine\Flusher;
use App\Parser\Entity\Task\TasksRepository;

final class Handler
{
    public function __construct(
        private readonly TasksRepository $tasks,
        private readonly Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $ids = $command->ids;

        if (empty($ids)) {
            return;
        }

        $this->tasks->remove($ids);

        $this->flusher->flush();
    }
}
