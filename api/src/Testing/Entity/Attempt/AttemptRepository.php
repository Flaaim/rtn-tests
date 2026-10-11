<?php

declare(strict_types=1);

namespace App\Testing\Entity\Attempt;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;

final class AttemptRepository
{
    private readonly EntityRepository $repo;

    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        $this->repo = $em->getRepository(Attempt::class);
    }

    public function add(Attempt $attempt): void
    {
        $this->em->persist($attempt);
    }

    public function get(AttemptId $id): Attempt
    {
        $attempt = $this->repo->find($id);
        if (null === $attempt) {
            throw new DomainException('Attempt not found.');
        }
        /** @var Attempt $attempt */
        return $attempt;
    }

    public function findUserProcessedAttempt(string $userId, string $testId, int $ticketNumber): ?Attempt
    {
        return $this->repo->findOneBy([
            'userId' => $userId,
            'status' => Status::STATUS_IN_PROGRESS,
            'testId' => $testId,
            'ticketNumber' => $ticketNumber,
        ]);
    }

    public function countByUserId(string $userId): int
    {
        return (int)$this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.userId = :user')
            ->setParameter('user', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
