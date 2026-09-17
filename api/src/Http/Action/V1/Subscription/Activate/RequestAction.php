<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Subscription\Activate;

use App\Infrastructure\Http\Validator\Validator;
use App\Subscription\Command\Activate\Command;
use App\Subscription\Command\Activate\Handler;
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

    #[Route('v1/subscriptions', name: 'subscriptions.activate', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(Request $request): Response
    {
        $user = $this->security->getUser();
        if (null === $user) {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }
        $userId = $user->getUserIdentifier();
        $body = $request->toArray();
        $durationDays = (int)($body['durationDays'] ?? 0);
        $plan  = $body['plan'] ?? '';

        $command = new Command($userId, $durationDays, $plan);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonResponse(201, Response::HTTP_CREATED);
    }
}
