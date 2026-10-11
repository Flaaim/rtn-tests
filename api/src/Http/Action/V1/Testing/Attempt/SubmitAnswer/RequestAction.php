<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Testing\Attempt\SubmitAnswer;

use App\Infrastructure\Http\Validator\Validator;
use App\Testing\Command\Attempt\SubmitAnswer\Command;
use App\Testing\Command\Attempt\SubmitAnswer\Handler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RequestAction
{
    public function __construct(
        private Handler $handler,
        private Validator $validator,
        private Security $security,
    ) {}

    #[Route('/v1/testing/attempts/{id}/submit', name: 'testing.attempt.submit', methods: ['POST'])]
    public function __invoke(Request $request, string $id): Response
    {
        $userId = $this->security->getUser()?->getUserIdentifier();
        $guestId = $request->headers->get('X-Guest-ID');
        $actorId = $userId ?? $guestId;

        if (null === $actorId) {
            return new JsonResponse(['error' => 'Access Denied'], Response::HTTP_FORBIDDEN);
        }

        $body = $request->toArray();
        $questionId = $body['questionId'] ?? '';
        $selectedAnswersIds = $body['selectedAnswersIds'] ?? [];

        $command = new Command($id, $questionId, $selectedAnswersIds);

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
