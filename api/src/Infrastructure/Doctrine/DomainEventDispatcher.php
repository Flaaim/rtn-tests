<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use App\Event\Entity\Event;
use App\SharedDomain\AggregateRoot;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AsDoctrineListener(event: Events::onFlush)]
final readonly class DomainEventDispatcher
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private SerializerInterface $serializer
    ) {}

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();
        $entities = [];

        foreach ($uow->getIdentityMap() as $classEntities) {
            foreach ($classEntities as $entity) {
                $entities[] = $entity;
            }
        }
        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $entities[] = $entity;
        }

        $classMetadata = $em->getClassMetadata(Event::class);

        foreach ($entities as $entity) {
            if ($entity instanceof AggregateRoot) {
                foreach ($entity->releaseEvents() as $domainEvent) {
                    $aggregateId = $entity->getAggregateId();

                    $eventEntity = new Event(
                        aggregateId: $aggregateId,
                        type: \get_class($domainEvent),
                        payload: $this->serializer->serialize($domainEvent, 'json')
                    );

                    $em->persist($eventEntity);
                    $uow->computeChangeSet($classMetadata, $eventEntity);

                    $this->messageBus->dispatch($domainEvent);
                }
            }
        }
    }
}
