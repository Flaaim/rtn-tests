<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment\Yookassa;

use App\Infrastructure\Payment\PaymentGatewayException;
use App\Infrastructure\Payment\PaymentGatewayInterface;
use App\Infrastructure\Payment\PaymentGatewayResult;
use Psr\Log\LoggerInterface;
use Throwable;
use YooKassa\Client;
use YooKassa\Model\Payment\Confirmation\ConfirmationRedirect;
use YooKassa\Model\Payment\PaymentInterface;

final readonly class YookassaPaymentGateway implements PaymentGatewayInterface
{
    public function __construct(
        private Client $client,
        private LoggerInterface $logger,
    ) {}

    public function createPayment(
        string $actor,
        string $amount,
        string $returnUrl,
        array $metadata = [],
    ): PaymentGatewayResult {
        try {
            $payment = $this->client->createPayment(
                [
                    'amount' => [
                        'value' => $amount,
                        'currency' => 'RUB',
                    ],
                    'confirmation' => [
                        'type' => 'redirect',
                        'return_url' => $returnUrl,
                    ],
                    'capture' => true,
                    'description' => 'Оплата подписки к тестам',
                    'metadata' => [
                        'actor' => $actor,
                        'payment_type' => $metadata['payment_type'],
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
                'actor' => $actor,
                'amount' => $amount,
                'type' => $metadata['payment_type'],
                'exception' => $exception,
            ]);

            throw new PaymentGatewayException('Failed to create YooKassa payment.', 0, $exception);
        }
    }
}
