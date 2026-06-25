<?php

namespace App\Parser\Entity\Task;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class TasksRepository
{
    private readonly EntityRepository $repo;
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        $this->repo = $em->getRepository(Task::class);
    }
    public function add(Task $task): void
    {
        $this->em->persist($task);
    }

    public function get(TaskId $id): Task
    {
        $task = $this->repo->find($id);
        if($task === null) {
            throw new \DomainException('Task not found.');
        }
        return $task;
    }
}
