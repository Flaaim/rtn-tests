<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Testing\Test\ChangeCipher;

use App\Infrastructure\Http\Validator\Validator;
use App\Testing\Command\Test\ChangeCipher\Command;
use App\Testing\Command\Test\ChangeCipher\Handler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RequestAction
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator
    ) {}

    #[Route('/v1/admin/testing/tests/{id}/change-cipher', name: 'admin.testing.test.change-cipher', methods: ['PUT'])]
    public function __invoke(string $id, Request $request): Response
    {
        $body = $request->toArray();

        $cipher = $body['cipher'] ?? '';

        $command = new Command($id, $cipher);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
