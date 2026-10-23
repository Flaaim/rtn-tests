<?php

declare(strict_types=1);

namespace App\Subscription\MessageHandler\Subscription;

use App\Subscription\Event\Subscription\SubscriptionPurchased;
use App\Subscription\Interfaces\ProfileInterface;
use App\Subscription\Service\PaymentConfirmedSender;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/** @psalm-suppress UnusedClass */
#[AsMessageHandler]
final readonly class SendEmailOnSubscriptionPurchasedHandler
{
    public function __construct(
        private PaymentConfirmedSender $sender,
        private ProfileInterface $profile
    ) {}

    public function __invoke(SubscriptionPurchased $event): void
    {
        $userId = $event->userId;

        $user = $this->profile->getDetail($userId);

        $email = $user->email;
        $ended = $event->ended;

        $this->sender->send($email, $ended);
    }
}
