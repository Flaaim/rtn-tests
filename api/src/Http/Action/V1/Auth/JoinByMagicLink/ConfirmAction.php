<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\JoinByMagicLink;

use App\Auth\Command\JoinByMagicLink\Confirm\Command;
use App\Auth\Command\JoinByMagicLink\Confirm\Handler;
use App\Infrastructure\Http\Validator\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class ConfirmAction
{
    public function __construct(
        private Handler $handler,
        private Validator $validator
    ) {}

    #[Route('/v1/auth/join/magic/confirm', name: 'auth.join.magic.confirm', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $guestId = $request->headers->get('X-Guest-ID');
        $body = $request->toArray();
        $token = (string)($body['token'] ?? '');

        $command = new Command($token, $guestId);

        $this->validator->validate($command);

        $accessToken = $this->handler->handle($command);

        return new JsonResponse(['access_token' => $accessToken], Response::HTTP_CREATED);
    }
}
