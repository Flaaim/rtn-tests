<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Parser\Launch;

use App\Infrastructure\Http\Validator\Validator;
use App\Parser\Command\LaunchParse\Command;
use App\Parser\Command\LaunchParse\Handler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RequestAction
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator
    ) {}

    #[Route('/v1/parser/launch', name: 'parser.launch', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $body = $request->toArray();

        $parserId = (string)($body['parserId'] ?? '');
        $branchId = (string)($body['branchId'] ?? '');
        $ticketId = (string)($body['ticketId'] ?? '');

        $command = new Command($parserId, $branchId, $ticketId);

        $this->validator->validate($command);

        $taskId = $this->handler->handle($command);

        return new JsonResponse($taskId, Response::HTTP_CREATED);
    }
}
