<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Subscription\Get;

use App\Infrastructure\Http\Validator\Validator;
use App\Subscription\Query\Subscription\Get\Query;
use App\Subscription\Query\Subscription\Get\QueryHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class RequestAction
{
    public function __construct(
        private QueryHandler $handler,
        private Validator $validator
    ) {}

    #[Route('/v1/admin/subscriptions/{id}', name: 'admin.subscriptions.get', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(string $id): Response
    {
        $query = new Query($id);

        $this->validator->validate($query);

        $result = $this->handler->handle($query);

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
