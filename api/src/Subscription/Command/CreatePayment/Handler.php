<?php

declare(strict_types=1);

namespace App\Subscription\Command\CreatePayment;

use App\Infrastructure\Doctrine\Flusher;
use App\Subscription\Entity\Payment\Amount;
use App\Subscription\Entity\Payment\Payment;
use App\Subscription\Entity\Payment\PaymentId;
use App\Subscription\Entity\Payment\PaymentRepository;
use App\Subscription\Entity\Payment\PaymentStatus;
use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Service\Payment\PaymentGatewayInterface;

final readonly class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private PaymentGatewayInterface $paymentGateway,
        private PaymentRepository $payments,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): array
    {
        $paymentId = PaymentId::generate();
        $userId = $command->userId;
        $plan = Plan::from($command->plan);
        $amount = new Amount($command->amount);
        $status = PaymentStatus::PENDING;
        $durationDays = $command->durationDays;

        $returnUrl = $this->appendQueryParameter(
            $command->returnUrl,
            'paymentId',
            $paymentId->getValue(),
        );

        $gatewayResult = $this->paymentGateway->createPayment(
            $userId,
            $plan,
            $amount,
            $durationDays,
            $returnUrl,
        );

        $payment = new Payment(
            $paymentId,
            $gatewayResult->externalId,
            $userId,
            $plan,
            $status,
            $amount,
            $durationDays
        );

        $this->payments->add($payment);

        $this->flusher->flush();

        return [
            'paymentId' => $payment->getId()->getValue(),
            'confirmationUrl' => $gatewayResult->confirmationUrl,
        ];
    }

    private function appendQueryParameter(string $url, string $key, string $value): string
    {
        $separator = str_contains($url, '?') ? '&' : '?';

        return $url . $separator . rawurlencode($key) . '=' . rawurlencode($value);
    }
}
