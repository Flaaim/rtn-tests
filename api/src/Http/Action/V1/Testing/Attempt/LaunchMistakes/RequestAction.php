<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Testing\Attempt\LaunchMistakes;

use App\Infrastructure\Http\Validator\Validator;
use App\Testing\Command\Attempt\LaunchMistakes\Command;
use App\Testing\Command\Attempt\LaunchMistakes\Handler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class RequestAction
{
    public function __construct(
        private Handler $handler,
        private Validator $validator,
        private Security $security
    ) {}

    #[Route('/v1/testing/attempts/mistakes', name: 'testing.attempt.launch.mistakes', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(Request $request): Response
    {
        $user = $this->security->getUser();
        if (null === $user) {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }
        $userId = $user->getUserIdentifier();
        $body = $request->toArray();

        $testId = $body['testId'] ?? '';

        $command = new Command($userId, $testId);

        $this->validator->validate($command);

        $attemptId = $this->handler->handle($command);

        return new JsonResponse(['attemptId' => $attemptId], Response::HTTP_CREATED);
    }
}
