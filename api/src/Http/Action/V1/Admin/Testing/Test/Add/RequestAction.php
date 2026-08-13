<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Testing\Test\Add;

use App\Infrastructure\Http\Validator\Validator;
use App\Testing\Command\Test\Add\Command;
use App\Testing\Command\Test\Add\Handler;
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

    #[Route('/v1/admin/testing/tests', name: 'admin.testing.tests.add', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request): Response
    {
        $body = $request->toArray();

        $command = new Command(
            name: $body['name'] ?? '',
            cipher: $body['cipher'] ?? '',
            description: $body['description'] ?? '',
            numberOfTickets: $body['numberOfTickets'] ?? 0,
            numberQuestionsInTicket: $body['numberQuestionsInTicket'] ?? 0,
            allowedMistakes: $body['allowedMistakes'] ?? 0,
            courseIds: $body['courseIds'] ?? []
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new Response(null, Response::HTTP_CREATED);
    }
}
