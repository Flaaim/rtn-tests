<?php

declare(strict_types=1);

namespace App\Parser\Entity\Task;



use App\Parser\Event\TaskCreated;
use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;

final class Task implements AggregateRoot
{
    use EventTrait;

    private Status $status;
    public function __construct(
        private TaskId $taskId,
        private ?string $result = null,
    ) {

        $this->status = Status::processing();

        $this->recordEvent(new TaskCreated($this->taskId->getValue()));
    }

    public function getId(): TaskId
    {
        return $this->taskId;
    }
    public function getStatus(): Status
    {
        return $this->status;
    }
    public function getResult(): ?string
    {
        return $this->result;
    }


}
