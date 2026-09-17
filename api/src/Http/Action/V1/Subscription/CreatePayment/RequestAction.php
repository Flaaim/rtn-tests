<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Subscription\CreatePayment;

use App\Infrastructure\Http\Validator\Validator;
use App\Subscription\Command\CreatePayment\Command;
use App\Subscription\Command\CreatePayment\Handler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class RequestAction
{
    public function __construct(
        private Handler $handler,
        private Validator $validator,
        private Security $security,
    ) {}

    #[Route('v1/subscriptions/payments', name: 'subscriptions.payment.create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(Request $request): Response
    {
        $user = $this->security->getUser();
        if (null === $user) {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }
        $userId = $user->getUserIdentifier();
        $body = $request->toArray();
        $plan = (string)($body['plan'] ?? '');
        $amount = (string)($body['amount'] ?? '');
        $durationDays = (int)($body['durationDays'] ?? 0);
        $returnUrl = (string)($body['returnUrl'] ?? '');

        $command = new Command(
            plan: $plan,
            amount: $amount,
            durationDays: $durationDays,
            userId: $userId,
            returnUrl: $returnUrl,
        );

        $this->validator->validate($command);

        $result = $this->handler->handle($command);

        return new JsonResponse($result, Response::HTTP_CREATED);
    }
}
