<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Auth\ForceChangePassword;

use App\Auth\Command\ForceChangePassword\Command;
use App\Auth\Command\ForceChangePassword\Handler;
use App\Infrastructure\Http\Validator\Validator;
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

    #[Route('/v1/admin/auth/{id}/change-password', name: 'admin.auth.change.password', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(string $id, Request $request): Response
    {
        $body = $request->toArray();
        $password = $body['password'] ?? '';

        $command = new Command(
            $id,
            $password
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
