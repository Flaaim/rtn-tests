<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Recipient;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;

final class RecipientRepository
{
    private readonly EntityRepository $repo;

    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        $this->repo = $em->getRepository(Recipient::class);
    }

    public function add(RecipientId $recipientId): void
    {
        $this->em->persist($recipientId);
    }

    public function get(RecipientId $recipientId): Recipient
    {
        $recipient = $this->repo->find($recipientId);
        if (null === $recipient) {
            throw new DomainException('Recipient not found.');
        }
        return $recipient;
    }
}
