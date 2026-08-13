<?php

declare(strict_types=1);

namespace App\Testing\MessageHandler;

use App\Testing\Event\TestActivated;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/** @psalm-suppress UnusedClass */
#[AsMessageHandler]
final class LogOnTestActivatedHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(TestActivated $event): void
    {
        $testId = $event->id;

        $this->logger->info('Test activated: ', ['testId' => $testId]);
    }
}
