<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Parser\GetParser;

use App\Infrastructure\Http\Validator\Validator;
use App\Parser\Query\Parser\GetOne\Query;
use App\Parser\Query\Parser\GetOne\QueryHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RequestAction
{
    public function __construct(
        private readonly QueryHandler $handler,
        private readonly Validator $validator
    ) {}

    #[Route('v1/admin/parsers/{id}', name: 'admin.parser.get', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(string $id): Response
    {
        $query = new Query($id);

        $this->validator->validate($query);

        $parser = $this->handler->handle($query);

        return new JsonResponse($parser, Response::HTTP_OK);
    }
}
