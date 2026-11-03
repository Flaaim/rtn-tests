<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Product\GetOne;

use App\CourseAnswers\Query\Product\GetOne\Query;
use App\CourseAnswers\Query\Product\GetOne\QueryHandler;
use App\Infrastructure\Http\Validator\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class RequestAction
{
    public function __construct(
        private QueryHandler $queryHandler,
        private Validator $validator
    ) {}

    #[Route('/v1/admin/products/{id}', name: 'admin.product.get.one', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(string $id): Response
    {
        $query = new Query($id);
        $this->validator->validate($query);

        $result = $this->queryHandler->handle($query);

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
