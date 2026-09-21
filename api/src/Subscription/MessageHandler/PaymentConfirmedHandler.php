<?php

declare(strict_types=1);

namespace App\Subscription\MessageHandler;

use App\Infrastructure\Doctrine\Flusher;
use App\Subscription\Command\Activate\Command;
use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Entity\Subscription\SubscriptionRepository;
use App\Subscription\Event\Payment\PaymentConfirmed;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

/** @psalm-suppress UnusedClass */
#[AsMessageHandler]
final readonly class PaymentConfirmedHandler
{
    public function __construct(
        private SubscriptionRepository $subscriptions,
        private Flusher $flusher,
        private MessageBusInterface $messageBus,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(PaymentConfirmed $event): void
    {
        $activeSubscription = $this->subscriptions->findActiveByUserId($event->userId);

        if (null !== $activeSubscription) {
            $activeSubscription->extend($event->durationDays);
            $this->flusher->flush();

            $this->logger->info('User Subscription extended after payment confirmation.', [
                'paymentId' => $event->paymentId,
                'userId' => $event->userId,
                'plan' => $event->plan,
                'durationDays' => $event->durationDays,
            ]);
            return;
        }

        $this->messageBus->dispatch(new Command(
            $event->userId,
            $event->durationDays,
            Plan::BASIC->value
        ));

        $this->logger->info('User Subscription activated after payment confirmation.', [
            'paymentId' => $event->paymentId,
            'userId' => $event->userId,
            'plan' => $event->plan,
            'durationDays' => $event->durationDays,
        ]);
    }
}
