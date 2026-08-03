<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Task\Delete;

use App\Infrastructure\Http\Validator\Validator;
use App\Parser\Command\Task\Delete\Command;
use App\Parser\Command\Task\Delete\Handler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RequestAction
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator,
    ) {}

    #[Route('/v1/admin/tasks', name: 'admin.task.delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request): Response
    {
        $ids = $request->query->all('ids');

        $ids = array_filter(array_map('trim', $ids));

        $command = new Command($ids);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
