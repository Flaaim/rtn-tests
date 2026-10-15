<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\JoinByMagicLink;

use App\Auth\Command\JoinByMagicLink\Command;
use App\Auth\Command\JoinByMagicLink\Handler;
use App\Infrastructure\Http\Validator\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RequestAction
{
    public function __construct(
        private Handler $handler,
        private Validator $validator
    ) {}

    #[Route('/v1/auth/join/magic/request', name: 'auth.join.magic.request', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $body = $request->toArray();

        $email = (string)($body['email'] ?? '');

        $command = new Command($email);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
