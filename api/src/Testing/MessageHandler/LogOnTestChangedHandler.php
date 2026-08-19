<?php

declare(strict_types=1);

namespace App\Testing\MessageHandler;

use App\Testing\Event\TestChanged;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/** @psalm-suppress UnusedClass */
#[AsMessageHandler]
final class LogOnTestChangedHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(TestChanged $event): void
    {
        $testId = $event->id;
        $message = $event->message;

        $this->logger->info('Test changed: ' . $message, ['testId' => $testId]);
    }
}
