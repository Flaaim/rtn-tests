<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Testing\Analytics\GetPopular;

use App\Infrastructure\Http\Validator\Validator;
use App\Testing\Query\Analytics\GetPopularTests\Query;
use App\Testing\Query\Analytics\GetPopularTests\QueryHandler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RequestAction
{
    public function __construct(
        private QueryHandler $handler,
        private Validator $validator,
        private Security $security,
    ) {}

    #[Route('/v1/testing/analytics/popular', name: 'testing.analytics.popular', methods: ['GET'])]
    public function __invoke(): Response
    {
        $user = $this->security->getUser();
        if (null === $user) {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }

        $userId = $user->getUserIdentifier();

        $query = new Query($userId);

        $this->validator->validate($query);

        $result = $this->handler->handle($query);

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
