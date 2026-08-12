<?php

declare(strict_types=1);

namespace App\Testing\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class TestRepository
{
    private readonly EntityRepository $repo;

    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        $this->repo = $em->getRepository(Test::class);
    }

    public function add(Test $test): void
    {
        $this->em->persist($test);
    }
}
