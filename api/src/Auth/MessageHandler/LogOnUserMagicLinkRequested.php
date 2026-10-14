<?php

declare(strict_types=1);

namespace App\Auth\MessageHandler;

use App\Auth\Event\MagicLinkRequested;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class LogOnUserMagicLinkRequested
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(MagicLinkRequested $event): void
    {
        $email = $event->email;

        $this->logger->info('User magic link requested ' . $email);
    }
}
