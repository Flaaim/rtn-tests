<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Task\GetTask;

use App\Infrastructure\Http\Validator\Validator;
use App\Parser\Query\Task\GetOne\Query;
use App\Parser\Query\Task\GetOne\QueryHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RequestAction
{
    public function __construct(
        private readonly QueryHandler $handler,
        private readonly Validator $validator
    ) {}

    #[Route('/v1/admin/tasks/{id}', name: 'admin.task.get', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(string $id): Response
    {
        $query = new Query($id);

        $this->validator->validate($query);

        $task = $this->handler->handle($query);

        return new JsonResponse($task);
    }
}
