<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Admin\Product\ChangeAmount;

use App\CourseAnswers\Command\Product\ChangeAmount\Command;
use App\CourseAnswers\Command\Product\ChangeAmount\Handler;
use App\Infrastructure\Http\Validator\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class RequestAction
{
    public function __construct(
        private Handler $handler,
        private Validator $validator
    ) {}

    #[Route('/v1/admin/products/{id}/change-amount', name: 'admin.product.change.amount', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function __invoke(Request $request, string $id): Response
    {
        $body = $request->toArray();

        $amount = (string)($body['amount'] ?? '');

        $command = new Command($id, $amount);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
