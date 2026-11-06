<?php

declare(strict_types=1);

namespace App\CourseAnswers\Command\Payment\Confirm;

use App\CourseAnswers\Entity\Payment\PaymentRepository;
use App\Infrastructure\Doctrine\Flusher;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(method: 'handle')]
final readonly class Handler
{
    public function __construct(
        private PaymentRepository $payments,
        private Flusher $flusher,
        private LoggerInterface $logger
    ) {}

    public function handle(Command $command): void
    {
        $payment = $this->payments->findByExternalId($command->externalId);

        if (null === $payment) {
            $this->logger->warning(\sprintf('Payment with id: %s not found.', $command->externalId));
            return;
        }

        $payment->confirm();
        $this->flusher->flush();
    }
}
