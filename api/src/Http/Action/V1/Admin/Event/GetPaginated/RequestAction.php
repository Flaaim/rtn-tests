<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Event\GetPaginated;

use App\Event\Query\Event\GetPaginated\Query;
use App\Event\Query\Event\GetPaginated\QueryHandler;
use App\Infrastructure\Http\Validator\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RequestAction
{
    public function __construct(
        private readonly QueryHandler $handler,
        private readonly Validator $validator,
    ) {}

    #[Route('/v1/admin/events', name: 'admin.event.get.paginated', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request): Response
    {
        $queryParams = $request->query->all();
        $page = isset($queryParams['page']) && is_numeric($queryParams['page']) ? (int)$queryParams['page'] : 1;
        $limit = isset($queryParams['limit']) && is_numeric($queryParams['limit']) ? (int)$queryParams['limit'] : 50;
        $search = isset($queryParams['search']) && \is_string($queryParams['search']) ? $queryParams['search'] : null;

        $aggregateId = $request->query->get('aggregateId');
        $searchTerm = $aggregateId ?? $search;

        $query = new Query($page, $limit, $searchTerm);

        $this->validator->validate($query);

        $courses = $this->handler->handle($query);

        return new JsonResponse($courses, Response::HTTP_OK);
    }
}
