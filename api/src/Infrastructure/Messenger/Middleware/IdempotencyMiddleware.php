<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\Middleware;

use App\Event\Entity\ProcessedEvent;
use App\Infrastructure\Messenger\Stamp\MessageIdStamp;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

final readonly class IdempotencyMiddleware implements MiddlewareInterface
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $receivedStamp = $envelope->last(ReceivedStamp::class);

        if (null === $receivedStamp) {
            if (null === $envelope->last(MessageIdStamp::class)) {
                $envelope = $envelope->with(new MessageIdStamp(Uuid::uuid4()->toString()));
            }

            return $stack->next()->handle($envelope, $stack);
        }

        if ('async' !== $receivedStamp->getTransportName()) {
            return $stack->next()->handle($envelope, $stack);
        }

        $idStamp = $envelope->last(MessageIdStamp::class);
        if (null === $idStamp) {
            return $stack->next()->handle($envelope, $stack);
        }

        // Проверяем, не обрабатывали ли мы это сообщение ранее
        if (null !== $this->em->find(ProcessedEvent::class, $idStamp->id)) {
            return $envelope; // Дубль — молча пропускаем
        }

        return $this->em->wrapInTransaction(function () use ($envelope, $stack, $idStamp): Envelope {
            $envelope = $stack->next()->handle($envelope, $stack);

            $this->em->persist(new ProcessedEvent($idStamp->id));
            $this->em->flush();

            return $envelope;
        });
    }
}
