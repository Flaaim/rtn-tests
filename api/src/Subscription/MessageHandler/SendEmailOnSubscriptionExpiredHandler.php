<?php

declare(strict_types=1);

namespace App\Subscription\MessageHandler;

use App\Profile\Api\GetDetail\QueryHandlerApi;
use App\Subscription\Event\Subscription\SubscriptionExpired;
use App\Subscription\Service\SubscriptionExpiredSender;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/** @psalm-suppress UnusedClass */
#[AsMessageHandler]
final readonly class SendEmailOnSubscriptionExpiredHandler
{
    public function __construct(
        private QueryHandlerApi $profileApi,
        private SubscriptionExpiredSender $sender,
    ) {}

    public function __invoke(SubscriptionExpired $event): void
    {
        $userId = $event->userId;

        $user = $this->profileApi->getDetail($userId);

        $email = $user->email;

        $this->sender->send($email);
    }
}
