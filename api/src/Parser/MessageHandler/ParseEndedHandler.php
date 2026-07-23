<?php

declare(strict_types=1);

namespace App\Parser\MessageHandler;

use App\Parser\Event\ParseEnded;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ParseEndedHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(ParseEnded $event): void
    {
        $this->logger->info('Parse is ended: ' . $event->taskId);
    }
}
