<?php

declare(strict_types=1);

namespace App\Testing\Entity\Test;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class TestRepository
{
    private readonly EntityRepository $repo;

    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        $this->repo = $em->getRepository(Test::class);
    }

    public function add(Test $test): void
    {
        $this->em->persist($test);
    }

    public function hasBySlug(string $slug): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.slug = :slug')
            ->setParameter(':slug', $slug)
            ->getQuery()->getSingleScalarResult() > 0;
    }
}
