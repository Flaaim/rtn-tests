<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Testing\Attempt\Get;

use App\Infrastructure\Http\Validator\Validator;
use App\Testing\Query\Attempt\Get\Query;
use App\Testing\Query\Attempt\Get\QueryHandler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RequestAction
{
    public function __construct(
        private QueryHandler $handler,
        private Validator $validator,
        private Security $security,
    ) {}

    #[Route('/v1/testing/attempts/{id}', name: 'testing.attempt.get', methods: ['GET'])]
    public function __invoke(string $id, Request $request): Response
    {
        $userId = $this->security->getUser()?->getUserIdentifier();
        $guestId = $request->headers->get('X-Guest-ID');
        $actorId = $userId ?? $guestId;

        if (null === $actorId) {
            return new JsonResponse(['error' => 'Access Denied'], Response::HTTP_FORBIDDEN);
        }
        $query = new Query($id);

        $this->validator->validate($query);

        $attempt = $this->handler->handle($query);

        return new JsonResponse($attempt);
    }
}
