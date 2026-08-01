<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Parser\Create;

use App\Infrastructure\Http\Validator\Validator;
use App\Parser\Command\CreateParser\Command;
use App\Parser\Command\CreateParser\Handler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RequestAction
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator
    ) {}

    #[Route('/v1/admin/parsers', name: 'admin.parser.create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request): Response
    {
        $body = $request->toArray();
        $host = (string)($body['host'] ?? '');
        $login = (string)($body['login'] ?? '');
        $password = (string)($body['password'] ?? '');

        $command = new Command($host, $login, $password);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
