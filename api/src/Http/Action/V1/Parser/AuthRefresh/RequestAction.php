<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Parser\AuthRefresh;

use App\Infrastructure\Http\Validator\Validator;
use App\Parser\Command\AuthRefresh\Command;
use App\Parser\Command\AuthRefresh\Handler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RequestAction
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator
    ) {}

    #[Route('/v1/parsers/{parserId}/refresh', name: 'parser.refresh', methods: ['POST'])]
    public function __invoke(string $parserId): Response
    {
        $command = new Command($parserId);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
