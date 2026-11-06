<?php

declare(strict_types=1);

namespace App\CourseAnswers\Command\Payment\Fail;

use App\CourseAnswers\Entity\Payment\PaymentRepository;
use App\Infrastructure\Doctrine\Flusher;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(method: 'handle')]
final readonly class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private PaymentRepository $payments,
        private Flusher $flusher,
        private LoggerInterface $logger
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
