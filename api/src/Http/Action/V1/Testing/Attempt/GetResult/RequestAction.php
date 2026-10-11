<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Testing\Attempt\GetResult;

use App\Infrastructure\Http\Validator\Validator;
use App\Testing\Query\Attempt\GetResult\Query;
use App\Testing\Query\Attempt\GetResult\QueryHandler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RequestAction
{
    public function __construct(
        private readonly QueryHandler $queryHandler,
        private readonly Validator $validator,
        private readonly Security $security,
    ) {}

    #[Route('/v1/testing/attempts/{id}/result', name: 'testing.attempts.result', methods: ['GET'])]
    public function __invoke(string $id, Request $request): Response
    {
        $userId = $this->security->getUser()?->getUserIdentifier();
        $guestId = $request->headers->get('X-Guest-ID');
        $actorId = $userId ?? $guestId;

        if (null === $actorId) {
            return new JsonResponse(['error' => 'Access Denied'], Response::HTTP_FORBIDDEN);
        }

        $query = new Query($id, $actorId);

        $this->validator->validate($query);

        $result = $this->queryHandler->handle($query);

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
