<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Recipient\Remove;

use App\CourseAnswers\Command\Recipient\Remove\Command;
use App\CourseAnswers\Command\Recipient\Remove\Handler;
use App\Infrastructure\Http\Validator\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class RequestAction
{
    public function __construct(
        private Handler $queryHandler,
        private Validator $validator
    ) {}

    #[Route('/v1/admin/recipients/{id}/remove', name: 'admin.recipient.get.remove', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(string $id): Response
    {
        $query = new Command($id);
        $this->validator->validate($query);

        $this->queryHandler->handle($query);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
