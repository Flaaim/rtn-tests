<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Testing\Course\Get;

use App\Infrastructure\Http\Validator\Validator;
use App\Testing\Query\Course\GetOne\Fetcher;
use App\Testing\Query\Course\GetOne\Query;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RequestAction
{
    public function __construct(
        private readonly Fetcher $fetcher,
        private readonly Validator $validator
    ) {}

    #[Route('/v1/admin/testing/courses/{id}', name: 'admin.testing.courses.get.one', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(string $id): Response
    {
        $query = new Query($id);

        $this->validator->validate($query);

        $result = $this->fetcher->handle($query);

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
