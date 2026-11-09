<?php

declare(strict_types=1);

namespace App\Http\Action\V1\CourseAnswers\Payment\GetResult;

use App\CourseAnswers\Query\Payment\GetResult\Query;
use App\CourseAnswers\Query\Payment\GetResult\QueryHandler;
use App\Infrastructure\Http\Validator\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RequestAction
{
    public function __construct(
        private QueryHandler $queryHandler,
        private Validator $validator
    ) {}

    #[Route('/v1/products/payments/{externalId}/result', name: 'payment.products.result', methods: ['GET'])]
    public function __invoke(string $externalId): Response
    {
        $query = new Query($externalId);

        $this->validator->validate($query);

        $result = $this->queryHandler->handle($query);

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
