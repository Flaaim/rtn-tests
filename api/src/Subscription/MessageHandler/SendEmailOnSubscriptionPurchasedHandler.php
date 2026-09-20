<?php

declare(strict_types=1);

namespace App\Subscription\MessageHandler;

use App\Profile\Api\GetDetail\QueryHandlerApi;
use App\Subscription\Event\Subscription\SubscriptionPurchased;
use App\Subscription\Service\PaymentConfirmedSender;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SendEmailOnSubscriptionPurchasedHandler
{
    public function __construct(
        private PaymentConfirmedSender $sender,
        private QueryHandlerApi $profileApi
    ) {}

    public function __invoke(SubscriptionPurchased $event): void
    {
        $userId = $event->userId;

        $user = $this->profileApi->getDetail($userId);

        $email = $user->email;
        $ended = $event->ended;

        $this->sender->send($email, $ended);
    }
}
