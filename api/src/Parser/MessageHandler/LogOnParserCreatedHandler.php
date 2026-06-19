<?php

declare(strict_types=1);

namespace App\Parser\MessageHandler;

use App\Parser\Event\ParserCreated;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class LogOnParserCreatedHandler
{
    public function __construct(
      private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(ParserCreated $event): void
    {
        $host = $event->host;
        $this->logger->info('New parser created: '. $host);
    }
}
