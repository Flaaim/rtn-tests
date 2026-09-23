<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Testing\Category\ChangeDescription;

use App\Infrastructure\Http\Validator\Validator;
use App\Testing\Command\Category\ChangeDescription\Command;
use App\Testing\Command\Category\ChangeDescription\Handler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RequestAction
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator
    ) {}

    #[Route('/v1/admin/testing/categories/{id}/change-description', name: 'admin.testing.categories.change.description', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, string $id): Response
    {
        $body = $request->toArray();
        $description = $body['description'] ?? '';

        $command = new Command($id, $description);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
