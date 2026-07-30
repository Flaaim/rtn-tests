<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Parser\GetParsers;

use App\Parser\Query\Parser\GetParsers\Handler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RequestAction
{
    public function __construct(
        private readonly Handler $handler
    ) {}

    #[Route('/v1/parsers', name: 'parser.all', methods: ['GET'])]
    public function __invoke(): Response
    {
        $parsers = $this->handler->handle();

        return new JsonResponse($parsers);
    }
}
