<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Profile\GetFull;

use App\Infrastructure\Http\Validator\Validator;
use App\Profile\Query\GetFull\Query;
use App\Profile\Query\GetFull\QueryHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RequestAction
{
    public function __construct(
        private readonly QueryHandler $handler,
        private readonly Validator $validator
    ) {}

    #[Route(
        '/v1/admin/profiles/{id}',
        name: 'admin.profiles.get.one',
        requirements: ['id' => Requirement::UUID],
        methods: ['GET']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(string $id): Response
    {
        $query = new Query($id);

        $this->validator->validate($query);

        $result = $this->handler->handle($query);

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
