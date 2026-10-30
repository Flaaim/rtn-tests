<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Course\Course\RenderToFile;

use App\Course\Command\Course\RenderToFile\Command;
use App\Course\Command\Course\RenderToFile\Handler;
use App\Infrastructure\Http\Validator\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class RequestAction
{
    public function __construct(
        private Handler $handler,
        private Validator $validator,
    ) {}

    #[Route('/v1/admin/testing/courses/{id}/render', name: 'admin.testing.courses.render', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(string $id): Response
    {
        $command = new Command($id);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
