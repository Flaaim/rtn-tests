<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Subscription\Remove;

use App\Infrastructure\Http\Validator\Validator;
use App\Subscription\Command\Remove\Command;
use App\Subscription\Command\Remove\Handler;
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

    #[Route('/v1/admin/subscriptions/{id}', name: 'subscriptions.admin.remove', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(string $id): Response
    {

        $command = new Command($id);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonResponse(204, Response::HTTP_NO_CONTENT);
    }
}
