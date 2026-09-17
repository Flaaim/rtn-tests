<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment\Yookassa;

use App\Subscription\Entity\Payment\Amount;
use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Service\Payment\PaymentGatewayException;
use App\Subscription\Service\Payment\PaymentGatewayInterface;
use App\Subscription\Service\Payment\PaymentGatewayResult;
use Psr\Log\LoggerInterface;
use Throwable;
use YooKassa\Client;
use YooKassa\Model\Payment\Confirmation\ConfirmationRedirect;
use YooKassa\Model\Payment\PaymentInterface;

final class YookassaPaymentGateway implements PaymentGatewayInterface
{
    public function __construct(
        private Client $client,
        private LoggerInterface $logger,
    ) {}

    public function createPayment(
        string $userId,
        Plan $plan,
        Amount $amount,
        int $durationDays,
        string $returnUrl
    ): PaymentGatewayResult {
        try {
            $payment = $this->client->createPayment(
                [
                    'amount' => [
                        'value' => $amount->getValue(),
                        'currency' => $amount->getCurrency(),
                    ],
                    'confirmation' => [
                        'type' => 'redirect',
                        'return_url' => $returnUrl,
                    ],
                    'capture' => true,
                    'description' => 'Оплата подписки к тестам',
                    'metadata' => [
                        'user_id' => $userId,
                        'plan_id' => $plan->value,
                        'duration_days' => (string)$durationDays,
                    ],
                ],
                uniqid('', true),
            );

            if (!$payment instanceof PaymentInterface) {
                throw new PaymentGatewayException('YooKassa returned an unexpected payment response.');
            }

            $confirmation = $payment->getConfirmation();
            if (!$confirmation instanceof ConfirmationRedirect) {
                throw new PaymentGatewayException('YooKassa payment confirmation URL is missing.');
            }
            $confirmationUrl = $confirmation->getConfirmationUrl();
            $externalId = $payment->getId();

            if (null === $confirmationUrl || '' === $confirmationUrl || null === $externalId || '' === $externalId) {
                throw new PaymentGatewayException('YooKassa payment response is incomplete.');
            }

            return new PaymentGatewayResult($externalId, $confirmationUrl);
        } catch (PaymentGatewayException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            $this->logger->error('YooKassa payment creation failed.', [
                'userId' => $userId,
                'plan' => $plan->value,
                'amount' => $amount->getValue(),
                'exception' => $exception,
            ]);

            throw new PaymentGatewayException('Failed to create YooKassa payment.', 0, $exception);
        }
    }
}
