<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Testing\Test\ChangeCategory;

use App\Infrastructure\Http\Validator\Validator;
use App\Testing\Command\Test\ChangeCategory\Command;
use App\Testing\Command\Test\ChangeCategory\Handler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class RequestAction
{
    public function __construct(
        private Handler $handler,
        private Validator $validator
    ) {}

    #[Route('/v1/admin/testing/tests/{id}/change-category', name: 'admin.testing.test.change-category', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(string $id, Request $request): Response
    {
        $body = $request->toArray();

        $categoryId = (string)($body['categoryId'] ?? '');

        $command = new Command($id, $categoryId);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
