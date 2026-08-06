<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Testing\Course\GetAll;

use App\Testing\Query\Course\FindAll\Fetcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RequestAction
{
    public function __construct(
        private readonly Fetcher $fetcher
    ) {}

    #[Route('/v1/admin/testing/courses', name: 'admin.testing.courses.get', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(): Response
    {
        $courses = $this->fetcher->handle();

        return new JsonResponse($courses, Response::HTTP_OK);
    }
}
