<?php

declare(strict_types=1);

namespace App\Parser\Entity\Task;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;

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
        if (null === $task) {
            throw new DomainException('Task not found.');
        }
        return $task;
    }

    public function remove(array $ids): void
    {
        $qb = $this->repo->createQueryBuilder('t');

        $qb->delete()
            ->where('t.taskId IN (:ids)')
            ->setParameter('ids', $ids)

            ->getQuery()->execute();
    }
}
