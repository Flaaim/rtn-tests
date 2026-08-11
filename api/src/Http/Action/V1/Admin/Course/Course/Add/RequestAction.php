<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Course\Course\Add;

use App\Course\Command\Course\Add\Command;
use App\Course\Command\Course\Add\Handler;
use App\Infrastructure\Http\Validator\Validator;
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

    #[Route('/v1/admin/testing/courses', name: 'admin.testing.courses.add', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request): Response
    {
        $body = $request->toArray();
        $name = $body['name'] ?? '';
        $draft = $body['draft'] ?? '';
        $cipher = $body['cipher'] ?? '';

        $command = new Command($name, $draft, $cipher);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
