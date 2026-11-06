<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment\Webhook;

use App\CourseAnswers\Command\Payment\Confirm\Command as ConfirmProductCommand;
use App\CourseAnswers\Command\Payment\Fail\Command as FailProductCommand;
use App\Subscription\Command\ConfirmPayment\Command as ConfirmSubscriptionCommand;
use App\Subscription\Command\FailPayment\Command as FailSubscriptionCommand;

final class WebhookCommandFactory
{
    public function create(string $paymentType, string $event, string $externalId): ?object
    {
        return match ([$paymentType, $event]) {
            ['subscription', 'payment.succeeded'] => new ConfirmSubscriptionCommand($externalId),
            ['subscription', 'payment.canceled']  => new FailSubscriptionCommand($externalId),
            ['product', 'payment.succeeded']      => new ConfirmProductCommand($externalId),
            ['product', 'payment.canceled']       => new FailProductCommand($externalId),
            default => null,
        };
    }
}
