<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Testing\Course\Remove;

use App\Infrastructure\Http\Validator\Validator;
use App\Testing\Command\Course\Remove\Command;
use App\Testing\Command\Course\Remove\Handler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RequestAction
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator
    ) {}

    #[Route('/v1/admin/testing/courses/{id}', name: 'admin.testing.course.remove', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(string $id): Response
    {
        $command = new Command($id);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
