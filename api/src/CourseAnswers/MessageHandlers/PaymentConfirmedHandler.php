<?php

declare(strict_types=1);

namespace App\CourseAnswers\MessageHandlers;

use App\CourseAnswers\Command\Product\Send\Command;
use App\CourseAnswers\Event\Payment\PaymentConfirmed;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final readonly class PaymentConfirmedHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {}

    public function __invoke(PaymentConfirmed $event): void
    {
        $this->messageBus->dispatch(new Command(
            $event->productId,
            $event->email,
        ));
    }
}
