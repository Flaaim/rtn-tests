<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Task\GetTasks;

use App\Parser\Query\Task\GetTasks\Fetcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RequestAction
{
    public function __construct(
        private readonly Fetcher $handler
    ) {}

    #[Route('/v1/admin/tasks', name: 'admin.task.all', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(): Response
    {
        $tasks = $this->handler->fetch();

        return new JsonResponse($tasks);
    }
}
