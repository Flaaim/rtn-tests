<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\EventSubscriber;

use App\Infrastructure\Messenger\Stamp\MessageIdStamp;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\TransportNamesStamp;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;

final class OutboxForwarderSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        #[Autowire(service: 'messenger.transport.async_outbox')]
        private readonly ReceiverInterface $outboxReceiver,
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
            $envelope = $event->getEnvelope();
            $stamps = [new TransportNamesStamp(['async'])];

            if ($idStamp = $envelope->last(MessageIdStamp::class)) {
                $stamps[] = $idStamp;
            }

            $this->messageBus->dispatch($envelope->getMessage(), $stamps);

            $this->outboxReceiver->ack($envelope);
            $event->shouldHandle(false);
        }
    }
}
