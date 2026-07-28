<?php

declare(strict_types=1);

namespace App\Parser\MessageHandler;

use App\Parser\Event\ParseFailed;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class LogOnParseFailedHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(ParseFailed $event): void
    {
        $this->logger->error('Parse data failed: ' . $event->parseId . ' ' . $event->reason);
    }
}
