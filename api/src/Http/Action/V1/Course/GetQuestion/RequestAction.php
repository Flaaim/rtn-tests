<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Course\GetQuestion;

use App\Course\Query\Course\GetQuestion\Query;
use App\Course\Query\Course\GetQuestion\QueryHandler;
use App\Infrastructure\Http\Validator\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RequestAction
{
    public function __construct(
        private QueryHandler $handler,
        private Validator $validator,
    ) {}

    #[Route('/v1/questions/{id}', name: 'questions.get', methods: ['GET'])]
    public function __invoke(string $id): Response
    {
        $query = new Query($id);

        $this->validator->validate($query);

        $result = $this->handler->handle($query);

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
