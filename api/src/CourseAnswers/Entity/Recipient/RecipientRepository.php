<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Recipient;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;

final readonly class RecipientRepository
{
    private EntityRepository $repo;

    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private EntityManagerInterface $em
    ) {
        $this->repo = $em->getRepository(Recipient::class);
    }

    public function add(Recipient $recipient): void
    {
        $this->em->persist($recipient);
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
