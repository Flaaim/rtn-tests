<?php

declare(strict_types=1);

namespace App\Testing\MessageHandler;

use App\Testing\Event\TestCreated;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class LogOnTestCreatedHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(TestCreated $event): void
    {
        $testId = $event->id;

        $this->logger->info('Test created: ', ['testId' => $testId]);
    }
}
