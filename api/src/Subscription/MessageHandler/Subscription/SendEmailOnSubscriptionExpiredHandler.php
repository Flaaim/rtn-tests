<?php

declare(strict_types=1);

namespace App\Subscription\MessageHandler\Subscription;

use App\Subscription\Event\Subscription\SubscriptionExpired;
use App\Subscription\Interfaces\ProfileInterface;
use App\Subscription\Service\SubscriptionExpiredSender;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/** @psalm-suppress UnusedClass */
#[AsMessageHandler]
final readonly class SendEmailOnSubscriptionExpiredHandler
{
    public function __construct(
        private ProfileInterface $profile,
        private SubscriptionExpiredSender $sender,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(SubscriptionExpired $event): void
    {
        $userId = $event->userId;

        $email = $this->profile->getProfileEmail($userId);

        if (null === $email) {
            $this->logger->error('Profile email does not exist: ' . $userId);
            return;
        }

        $this->sender->send($email);
    }
}
