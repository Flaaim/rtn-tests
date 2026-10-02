<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Profile\Lookup;

use App\Profile\Query\GetLookup\QueryHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class RequestAction
{
    public function __construct(
        private QueryHandler $handler,
    ) {}

    #[Route('/v1/admin/profiles/lookup', name: 'admin.profiles.lookup', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(): Response
    {
        $result = $this->handler->handle();

        return new JsonResponse($result);
    }
}
