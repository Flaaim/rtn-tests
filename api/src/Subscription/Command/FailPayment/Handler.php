<?php

declare(strict_types=1);

namespace App\Subscription\Command\FailPayment;

use App\Infrastructure\Doctrine\Flusher;
use App\Subscription\Entity\Payment\PaymentRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/** @psalm-suppress UnusedClass */
#[AsMessageHandler(method: 'handle')]
final readonly class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private PaymentRepository $payments,
        private LoggerInterface $logger,
        private Flusher $flusher,
    ) {}

    /** @psalm-suppress PossiblyUnusedMethod */
    public function handle(Command $command): void
    {
        $payment = $this->payments->findByExternalId($command->externalId);

        if (null === $payment) {
            $this->logger->warning(\sprintf('Payment with id: %s not found.', $command->externalId));
            return;
        }
        $payment->fail();

        $this->flusher->flush();
    }
}
