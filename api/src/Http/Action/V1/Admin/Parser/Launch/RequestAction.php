<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Parser\Launch;

use App\Infrastructure\Http\Validator\Validator;
use App\Parser\Command\Parser\LaunchParse\Command;
use App\Parser\Command\Parser\LaunchParse\Handler;
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

    #[Route('/v1/admin/parsers/{parserId}/launch', name: 'admin.parser.launch', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, string $parserId): Response
    {
        $body = $request->toArray();

        $branchId = (string)($body['branchId'] ?? '');
        $ticketId = (string)($body['ticketId'] ?? '');

        $command = new Command($parserId, $branchId, $ticketId);

        $this->validator->validate($command);

        $taskId = $this->handler->handle($command);

        return new JsonResponse($taskId, Response::HTTP_CREATED);
    }
}
