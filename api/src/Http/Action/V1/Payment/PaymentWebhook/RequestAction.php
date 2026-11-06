<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Payment\PaymentWebhook;

use App\Infrastructure\Payment\Webhook\WebhookCommandFactory;
use App\Infrastructure\Payment\Yookassa\YookassaWebhookGuard;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

final readonly class RequestAction
{
    public function __construct(
        private YookassaWebhookGuard $webhookGuard,
        private LoggerInterface $logger,
        private WebhookCommandFactory $commandFactory,
        private MessageBusInterface $messageBus,
    ) {}

    #[Route('/v1/payment-events', name: 'payment.webhook', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        if (!$this->webhookGuard->isAllowed($request)) {
            $this->logger->warning('Rejected YooKassa webhook from untrusted IP.', [
                'clientIp' => $request->getClientIp(),
            ]);

            return new Response('Forbidden', Response::HTTP_FORBIDDEN);
        }

        $payload = $request->toArray();

        $event = (string)($payload['event'] ?? '');
        $object = $payload['object'] ?? null;

        if (!\is_array($object)) {
            return new Response('Bad Request', Response::HTTP_BAD_REQUEST);
        }

        $externalId = (string)($object['id'] ?? '');
        if ('' === $externalId) {
            return new Response('Bad Request', Response::HTTP_BAD_REQUEST);
        }

        $metadata = $payload['metadata'] ?? [];
        if (!\is_array($metadata) || empty($metadata) || !isset($metadata['payment_type'])) {
            return new Response('Bad Request', Response::HTTP_BAD_REQUEST);
        }

        try {
            $command = $this->commandFactory->create($metadata['payment_type'], $event, $externalId);
            if (null !== $command) {
                $this->messageBus->dispatch($command);
            } else {
                $this->logger->info('Unhandled webhook type or event.');
            }
        } catch (Throwable $throwable) {
            $this->logger->error('YooKassa webhook processing failed.', [
                'event' => $event,
                'externalId' => $externalId,
                'exception' => $throwable,
            ]);
            return new Response('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response('OK', Response::HTTP_OK);
    }
}
