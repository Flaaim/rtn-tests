<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\TransportNamesStamp;

final class OutboxForwarderSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageReceivedEvent::class => 'onMessageReceived',
        ];
    }

    public function onMessageReceived(WorkerMessageReceivedEvent $event): void
    {
        if ('async_outbox' === $event->getReceiverName()) {
            $message = $event->getEnvelope()->getMessage();

            $this->messageBus->dispatch($message, [
                new TransportNamesStamp(['async']),
            ]);

            $event->shouldHandle(false);
        }
    }
}
