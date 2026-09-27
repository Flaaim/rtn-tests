<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Subscription\GetByUser;

use App\Infrastructure\Http\Validator\Validator;
use App\Subscription\Query\Subscription\GetByUser\Query;
use App\Subscription\Query\Subscription\GetByUser\QueryHandler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class RequestAction
{
    public function __construct(
        private QueryHandler $queryHandler,
        private Validator $validator,
        private Security $security,
    ) {}

    #[Route('/v1/subscriptions', name: 'subscriptions.get.paginated', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(Request $request): Response
    {
        $user = $this->security->getUser();
        if (null === $user) {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }
        $queryParams = $request->query->all();
        $page = isset($queryParams['page']) && is_numeric($queryParams['page']) ? (int)$queryParams['page'] : 1;
        $limit = isset($queryParams['limit']) && is_numeric($queryParams['limit']) ? (int)$queryParams['limit'] : 15;

        $userId = $user->getUserIdentifier();

        $query = new Query($userId, $page, $limit);

        $this->validator->validate($query);

        $result = $this->queryHandler->handle($query);

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
