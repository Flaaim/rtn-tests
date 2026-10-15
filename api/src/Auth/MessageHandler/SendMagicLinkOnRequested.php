<?php

declare(strict_types=1);

namespace App\Auth\MessageHandler;

use App\Auth\Entity\User\Email;
use App\Auth\Event\MagicLinkRequested;
use App\Auth\Service\MagicLinkSender;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SendMagicLinkOnRequested
{
    public function __construct(
        private MagicLinkSender $sender,
        private LoggerInterface $logger
    ) {}

    public function __invoke(MagicLinkRequested $event): void
    {
        $email = $event->email;
        $token = $event->token;

        $this->sender->send(new Email($email), $token);

        $this->logger->info(
            'Magic link requested: ' . $email,
        );
    }
}
