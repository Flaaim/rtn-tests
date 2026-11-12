<?php

declare(strict_types=1);

namespace App\Event;

use Doctrine\ORM\EntityManagerInterface;

final class EventRepository
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function add(Event $event): void
    {
        $this->em->persist($event);
    }
}
