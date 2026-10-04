<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Subscription\Assign;

use App\Infrastructure\Http\Validator\Validator;
use App\Subscription\Command\Assign\Command;
use App\Subscription\Command\Assign\Handler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class RequestAction
{
    public function __construct(
        private Handler $handler,
        private Validator $validator
    ) {}

    #[Route('/v1/admin/subscriptions', name: 'subscriptions.admin.assign', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request): Response
    {
        $body = $request->toArray();
        $userId = (string)($body['userId'] ?? '');
        $periodStart = (string)($body['periodStart'] ?? '');
        $periodEnd = (string)($body['periodEnd'] ?? '');
        $plan  = $body['plan'] ?? '';

        $command = new Command($userId, $plan, $periodStart, $periodEnd);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonResponse(201, Response::HTTP_CREATED);
    }
}
