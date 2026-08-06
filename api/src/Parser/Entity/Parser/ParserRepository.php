<?php

declare(strict_types=1);

namespace App\Parser\Entity\Parser;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class ParserRepository
{
    private EntityRepository $repo;

    public function __construct(
        private EntityManagerInterface $em
    ) {
        $this->repo = $em->getRepository(Parser::class);
    }

    public function add(Parser $parser): void
    {
        $this->em->persist($parser);
    }

    public function hasByHost(Host $host): bool
    {
        $parser = $this->repo->findOneBy(['host' => $host]);
        if (null === $parser) {
            return false;
        }
        return true;
    }

    public function findByHost(Host $host): ?Parser
    {
        return $this->repo->findOneBy(['host' => $host]);
    }

    public function find(ParserId $parserId): ?Parser
    {
        return $this->repo->findOneBy(['id' => $parserId]);
    }

    public function remove(Parser $parser): void
    {
        $this->em->remove($parser);
    }
}
