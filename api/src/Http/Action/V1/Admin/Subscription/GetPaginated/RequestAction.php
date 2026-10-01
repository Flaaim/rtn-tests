<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Subscription\GetPaginated;

use App\Infrastructure\Http\Validator\Validator;
use App\Subscription\Query\Subscription\GetPaginated\Query;
use App\Subscription\Query\Subscription\GetPaginated\QueryHandler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class RequestAction
{
    public function __construct(
        private QueryHandler $handler,
        private Validator $validator,
        private Security $security,
    ) {}

    #[Route('/v1/admin/subscriptions', name: 'admin.subscriptions.get.paginated', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request): Response
    {
        $user = $this->security->getUser();
        if (null === $user) {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }

        $queryParams = $request->query->all();
        $page = isset($queryParams['page']) && is_numeric($queryParams['page']) ? (int)$queryParams['page'] : 1;
        $limit = isset($queryParams['limit']) && is_numeric($queryParams['limit']) ? (int)$queryParams['limit'] : 25;
        $search = isset($queryParams['search']) && \is_string($queryParams['search']) ? $queryParams['search'] : null;

        $command = new Query($page, $limit, $search);

        $this->validator->validate($command);

        $result = $this->handler->handle($command);

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
