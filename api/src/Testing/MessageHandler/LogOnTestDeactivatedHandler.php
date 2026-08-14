<?php

declare(strict_types=1);

namespace App\Testing\MessageHandler;

use App\Testing\Event\TestDeactivated;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/** @psalm-suppress UnusedClass */
#[AsMessageHandler]
final class LogOnTestDeactivatedHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(TestDeactivated $event): void
    {
        $testId = $event->id;

        $this->logger->info('Test deactivated: ', ['testId' => $testId]);
    }
}
