<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Course\Course\Lookup;

use App\Course\Query\Course\GetLookup\QueryHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RequestAction
{
    public function __construct(
        private readonly QueryHandler $handler,
    ) {}

    #[Route('/v1/admin/testing/courses/lookup', name: 'admin.testing.courses.lookup', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(): Response
    {
        $result = $this->handler->handle();

        return new JsonResponse($result);
    }
}
