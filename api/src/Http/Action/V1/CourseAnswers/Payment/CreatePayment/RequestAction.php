<?php

declare(strict_types=1);

namespace App\Http\Action\V1\CourseAnswers\Payment\CreatePayment;

use App\CourseAnswers\Command\Payment\Create\Command;
use App\CourseAnswers\Command\Payment\Create\Handler;
use App\Infrastructure\Http\Validator\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RequestAction
{
    public function __construct(
        private Handler $handler,
        private Validator $validator
    ) {}

    #[Route('/v1/products/payments', name: 'payment.products.create', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $body = $request->toArray();
        $email = (string)($body['email'] ?? '');
        $productId = (string)($body['productId'] ?? '');
        $returnUrl = (string)($body['returnUrl'] ?? '');

        $command = new Command(
            productId: $productId,
            email: $email,
            returnUrl: $returnUrl
        );

        $this->validator->validate($command);

        $result = $this->handler->handle($command);

        return new JsonResponse($result, Response::HTTP_CREATED);
    }
}
