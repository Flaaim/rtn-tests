<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Testing\Test\GetPaginated;

use App\Infrastructure\Http\Validator\Validator;
use App\Testing\Query\Test\GetPaginated\Query;
use App\Testing\Query\Test\GetPaginated\QueryHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RequestAction
{
    public function __construct(
        private readonly QueryHandler $handler,
        private readonly Validator $validator,
    ) {}

    #[Route('/v1/admin/testing/tests', name: 'admin.testing.tests.get.paginated', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $queryParams = $request->query->all();
        $page = isset($queryParams['page']) && is_numeric($queryParams['page']) ? (int)$queryParams['page'] : 1;
        $limit = isset($queryParams['limit']) && is_numeric($queryParams['limit']) ? (int)$queryParams['limit'] : 15;
        $search = isset($queryParams['search']) && \is_string($queryParams['search']) ? $queryParams['search'] : null;

        $query = new Query($page, $limit, $search);

        $this->validator->validate($query);

        $courses = $this->handler->handle($query);

        return new JsonResponse($courses, Response::HTTP_OK);
    }
}
