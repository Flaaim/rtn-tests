<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Parser\GetParsers;

use App\Parser\Query\Parser\GetParsers\QueryHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RequestAction
{
    public function __construct(
        private readonly QueryHandler $handler
    ) {}

    #[Route('/v1/admin/parsers', name: 'admin.parser.all', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(): Response
    {
        $parsers = $this->handler->handle();

        return new JsonResponse($parsers);
    }
}
