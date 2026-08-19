<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Testing\Test\UpdateSettings;

use App\Infrastructure\Http\Validator\Validator;
use App\Testing\Command\Test\UpdateSettings\Command;
use App\Testing\Command\Test\UpdateSettings\Handler;
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

    #[Route('/v1/admin/testing/tests/{id}/update-settings', name: 'admin.testing.test.update-settings', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(string $id, Request $request): Response
    {
        $body = $request->toArray();

        $numberOfTickets = $body['numberOfTickets'] ?? 0;
        $numberQuestionsInTicket = $body['numberQuestionsInTicket'] ?? 0;
        $allowedMistakes = $body['allowedMistakes'] ?? 0;

        $command = new Command($id, $numberOfTickets, $numberQuestionsInTicket, $allowedMistakes);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
