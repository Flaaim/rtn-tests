<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Course\Course\Rename;

use App\Course\Command\Course\Rename\Command;
use App\Course\Command\Course\Rename\Handler;
use App\Infrastructure\Http\Validator\Validator;
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

    #[Route('/v1/admin/testing/courses/{id}/rename', name: 'admin.testing.courses.rename', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, string $id): Response
    {
        $body = $request->toArray();

        $name = $body['name'] ?? '';
        $draft = $body['cipher'] ?? '';

        $command = new Command($id, $name, $draft);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
