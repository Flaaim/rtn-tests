<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Testing\Attempt\Finish;

use App\Infrastructure\Http\Validator\Validator;
use App\Testing\Command\Attempt\Finish\Command;
use App\Testing\Command\Attempt\Finish\Handler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RequestAction
{
    public function __construct(
        private Handler $handler,
        private Validator $validator,
        private Security $security,
    ) {}

    #[Route('/v1/testing/attempts/{id}/finish', name: 'testing.attempt.finish', methods: ['PUT'])]
    public function __invoke(string $id, Request $request): Response
    {
        $userId = $this->security->getUser()?->getUserIdentifier();
        $guestId = $request->headers->get('X-Guest-ID');
        $actorId = $userId ?? $guestId;

        if (null === $actorId) {
            return new JsonResponse(['error' => 'Access Denied'], Response::HTTP_FORBIDDEN);
        }

        $command = new Command($id);
        $this->validator->validate($command);

        $this->handler->handle($command);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
